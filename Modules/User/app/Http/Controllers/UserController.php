<?php

namespace Modules\User\App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Modules\User\app\Models\User;
use Modules\User\app\Models\CustomerDetail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Modules\User\app\Http\Services\UserService;
use Illuminate\Http\JsonResponse;
use App\Rules\PANValidation;
use App\Rules\GSTValidation;
use App\Rules\UniquePanNumber;
use Mail;
use App\Helpers\Helper;
use App\Models\Notification;
use Modules\User\app\Models\AgentDetails;
use Modules\User\app\Models\Vendor;
use Modules\User\app\Models\VendorDetail;
use App\Models\Config;

class UserController extends Controller
{

    private UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }
    ///********************CUSTOMER SECTION*****************************/

    /**
     * Sign up/Sign in request by user
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function login(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'login' => 'required|string', // 'login' can be either email or mobile
        ]);
    
        if ($validator->fails()) {
            return response()->json(['res' => false, 'msg' => $validator->errors()->first(), 'data' => '']);
        }
    
        $login = $request->input('login');
    
        // Determine if login is email or mobile
        $isEmail = filter_var($login, FILTER_VALIDATE_EMAIL);
        $existingCustomer = User::where($isEmail ? 'email' : 'mobile', $login)->first();
    
        if ($existingCustomer) {
            if ($existingCustomer->user_type == 2) {
                $otp = $this->userService->generateOTP();
                $existingCustomer->otp = $otp;
                $existingCustomer->save();
    
                $data = [
                    'customer' => $existingCustomer->id,
                    'action' => 'login',
                    'otp' => $otp,
                ];
    
                // Send OTP via email if it's an email, or via SMS if it's a mobile number
                if ($isEmail) {
                    $mailSent = Helper::sendMail($existingCustomer->email, $data, 'mail.otp', 'Your OTP for Login');
                } else {
                    // Implement sending OTP via SMS for mobile numbers
                  $smsMessage = "Your OTP for mobile number verification is $otp. Do not share your OTP with anyone -From Wishmytour";
                  $smsSent = Helper::sendSMS($existingCustomer->mobile, $smsMessage, '1707172007352799266');
                }
    
                return response()->json(['res' => true, 'msg' => 'OTP sent successfully', 'data' => $data]);
            } else {
                return response()->json(['res' => false, 'msg' => 'User not authenticated', 'data' => '']);
            }
        } else {
            // Generate and save OTP
            $otp = $this->userService->generateOTP();
            $result = $this->userService->saveOTP($login, $otp);
    
            if ($result) {
                $lastInsertedId = \DB::getPdo()->lastInsertId();
                $data = [
                    'customer' => $lastInsertedId,
                    'action' => 'register',
                    'otp' => $otp,
                ];
    
                // Send OTP via email if it's an email, or via SMS if it's a mobile number
                if ($isEmail) {
                    $mailSent = Helper::sendMail($login, $data, 'mail.otp', 'Your OTP for Sign Up');
                } else {
                    // Implement sending OTP via SMS for mobile numbers
                   $smsMessage = "Your OTP for mobile number verification is $otp. Do not share your OTP with anyone -From Wishmytour";
                $smsSent = Helper::sendSMS($login, $smsMessage, '1707172007352799266');
            
                }
    
                return response()->json(['res' => true, 'msg' => 'OTP sent successfully', 'data' => $data]);
            } else {
                return response()->json(['res' => false, 'msg' => 'Failed to save OTP', 'data' => '']);
            }
        }
    }
    


    /**
     * Verify OTP for user login or registration
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */

    public function verifyOTP(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'otp' => 'required|numeric',
            'customer' => 'required',
            'action' => 'required|in:login,register',
        ]);

        if ($validator->fails()) {
            return response()->json(['res' => false, 'msg' => 'Validation failed', 'errors' => $validator->errors()]);
        }

        $otp = $request->input('otp');
        $customer = $request->input('customer');
        $action = $request->input('action');

        $user = User::find($customer);

        if (!$user) {
            return response()->json(['res' => false, 'msg' => 'User not found', 'data' => '']);
        }

        $existingcustomerDetail = CustomerDetail::where('user_id', $user->id)->first();

        if ($existingcustomerDetail) {
            $customerDetail = "exist";
        } else {
            $customerDetail = "notexist";
        }

        if ($user->otp != '0' && $user->otp == $otp) {
            if ($action == 'login') {

                Auth::loginUsingId($user->id);

                $data = [
                    'id' => $user->id,
                    'email' => !empty($user->email) ? $user->email : null,
                    'mobile' => !empty($user->mobile) ? $user->mobile : null,
                    'user_type' => 2,
                    'customerDetail' => $customerDetail,
                ];
                $token = $user->createToken('auth_token');

                return response()->json(['res' => true, 'msg' => 'Successfully logged in', 'data' => $data, 'token' => $token->accessToken]);
            } elseif ($action == 'register') {

                $userData = [
                    'email' => $user->email,
                    'mobile' => $user->mobile,

                ];

                //$newUser = User::create($userData);
                $otp2 = $this->userService->generateOTP();
                $user->update(['otp' => $otp2]); // Reset the OTP after successful registration
                $user->update(['user_type' => 2]);

                $data = [
                    'customer_id' => $user->id,
                    'email' => !empty($user->email) ? $user->email : null,
                    'mobile' => !empty($user->mobile) ? $user->mobile : null,
                    'user_type' => 2,
                    'customerDetail' => $customerDetail,

                ];


                $token = $user->createToken('auth_token');


                //Helper::sendNotification(34, "A new customer has been registered");
                return response()->json(['res' => true, 'msg' => 'Thank you for your patience', 'data' => $user, 'token' => $token->accessToken]);
            }


        } else {
            return response()->json(['res' => false, 'msg' => 'Provided confirmation code is not valid', 'data' => []]);
        }
    }

    /**
     * Add Register Details by user /customer
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function addCustomer(Request $request): JsonResponse
    {

        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'first_name' => 'nullable|string|max:100',
            'last_name' => 'nullable|string|max:100',
            'state' => 'nullable|integer',
            'city' => 'nullable|integer',
            'zipcode' => 'nullable|string|max:100',
            'gender' => 'nullable|string|max:100',
            'address' => 'nullable|string|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json(['res' => false, 'msg' => $validator->errors()->first(), 'data' => '']);
        }


        $userData = $request->only(['user_id', 'first_name', 'last_name', 'state', 'city', 'zipcode', 'gender','address']);


        $user = User::find($userData['user_id']);


        if (!$user || $user->user_type != 2) {
            return response()->json(['res' => false, 'msg' => 'Invalid user or user_type', 'data' => '']);
        }


        $existingCustomerDetail = CustomerDetail::where('user_id', $userData['user_id'])->first();

        if ($existingCustomerDetail) {
            return response()->json(['res' => false, 'msg' => 'CustomerDetail already exists for this user_id', 'data' => '']);
        }

        // $user->email =  $request->input('email');
        // $user->mobile =  $request->input('mobile');
        // $user->save();

        $user->update([
            'email' => $request->input('email', $user->email),
            'mobile' => $request->input('mobile', $user->mobile),
        ]);


        $customerDetails = CustomerDetail::create($userData);

        //   Helper::sendNotification(34, $customerDetails->first_name . " added details");

        return response()->json(['res' => true, 'msg' => 'Registration details saved successfully', 'data' => $customerDetails, 'user' => $user]);
    }

    /**
     * Register Details view by user
     *
     * @return mixed
     */
    public function registerDetailsView()
    {
        $user = Auth::user();

        if ($user) {

            $customerDetails = CustomerDetail::where('user_id', $user->id)->first();

            if ($customerDetails) {
                return response()->json(['res' => true, 'msg' => 'Customer details retrieved successfully', 'data' => $customerDetails, 'User' => $user]);
            } else {
                return response()->json(['res' => false, 'msg' => 'Customer details not found', 'data' => '']);
            }
        } else {
            return response()->json(['res' => false, 'msg' => 'User not authenticated', 'data' => '']);
        }
    }

    /**
     * Register Details Edit by user
     *
     * @param Request $request
     *
     */
    public function editCustomer(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'state' => 'nullable|integer',
            'city' => 'nullable|integer',
            'address' => 'nullable|string',
            'zipcode' => 'nullable|string|max:100',
            'gender' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['res' => false, 'msg' => $validator->errors()->first(), 'data' => '']);
        }

        $user = Auth::user();

        if ($user) {

            $customerDetails = CustomerDetail::where('user_id', $user->id)->first();

            if ($customerDetails) {

                $customerDetails->update($request->all());
                $user->email = $request->input('email');
                $user->mobile = $request->input('mobile');
                $user->save();


                //  dd(  $user);
                return response()->json(['res' => true, 'msg' => 'Customer details updated successfully', 'data' => $customerDetails, 'user' => $user]);
            } else {
                return response()->json(['res' => false, 'msg' => 'Customer details not found', 'data' => '']);
            }
        } else {
            return response()->json(['res' => false, 'msg' => 'User not authenticated', 'data' => '']);
        }
    }

    /**
     * Add PhotoId by user
     *
     * @param Request $request
     *
     */
    public function addPhotoId(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'id_type' => 'required|string|in:pancard,aadharcard',
            'id_number' => 'required|string|max:50',
            //  'id_verified' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['res' => false, 'msg' => $validator->errors()->first(), 'data' => '']);
        }

        $user = Auth::user();

        if ($user) {

            $updatedCustomerDetails = $this->userService->addPhotoId(
                $user->id,
                $request->input('id_type'),
                $request->input('id_number'),
            // $request->input('id_verified')
            );

            if ($updatedCustomerDetails) {
                return response()->json(['res' => true, 'msg' => 'ID proof details added successfully', 'data' => $updatedCustomerDetails]);
            } else {
                return response()->json(['res' => false, 'msg' => 'Customer details not found', 'data' => '']);
            }
        } else {
            return response()->json(['res' => false, 'msg' => 'User not authenticated', 'data' => '']);
        }
    }

    /**
     * Change Phone Number by user
     *
     * @param Request $request
     *
     */
    public function changePhoneNumber(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_phone_number' => 'required|string',
            'new_phone_number' => 'required|string|different:current_phone_number',
            'confirm_new_phone_number' => 'required|string|same:new_phone_number',
        ]);

        if ($validator->fails()) {
            return response()->json(['res' => false, 'msg' => $validator->errors()->first(), 'data' => ''], 200);
        }

        $user = Auth::user();

        if (!$user) {
            return response()->json(['res' => false, 'msg' => 'User not authenticated', 'data' => '']);
        }


        if ($user->mobile !== $request->input('current_phone_number')) {
            return response()->json(['res' => false, 'msg' => 'Current phone number does not match', 'data' => '']);
        }


        $otp = $this->userService->generateOTP();


        $user->otp = $otp;
        //  $user->mobile = $request->new_phone_number; // Store temp phone number directly in user object
        $user->save();


        $data = [
            'user_id' => $user->id,
            'action' => 'change_phone_number',
            'otp' => $otp,
            'temp_phone_number' => $request->new_phone_number,
        ];

        $smsMessage = "Your OTP for mobile number verification is $otp. Do not share your OTP with anyone -From Wishmytour";
        $smsSent = Helper::sendSMS($request->new_phone_number, $smsMessage, '1707172007352799266');
        return response()->json(['res' => true, 'msg' => 'OTP sent successfully', 'data' => $data]);
    }

    /**
     * Change Phone Number Verification by user
     *
     * @param Request $request
     *
     */
    public function verifyPhoneNumber(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'otp' => 'required|numeric',
            'temp_phone_number' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['res' => false, 'msg' => $validator->errors()->first(), 'data' => ''], 200);
        }

        $user = Auth::user();

        if (!$user) {
            return response()->json(['res' => false, 'msg' => 'User not authenticated', 'data' => '']);
        }


        if ($user->otp == $request->input('otp')) {

            $user->mobile = $request->temp_phone_number;
            $user->save();


            //  $user->temp_phone_number = null;
            // $user->save();

            return response()->json(['res' => true, 'msg' => 'Phone number changed successfully', 'data' => $user]);
        }

        return response()->json(['res' => false, 'msg' => 'Invalid OTP', 'data' => '']);
    }
    ///********************VENDOR SECTION*****************************/

    /**
     * Vendor Login
     *
     * @param Request $request
     * @return JsonResponse
     *
     */
    public function vendorLogin(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
           'email' => 'required_without:mobile|string',
           'mobile' => 'required_without:email|string',
            // 'email' => ['required_without:mobile', 'string', 'email', 'max:255', 'unique:users,email'],
            // 'mobile' => ['required_without:email', 'string', 'max:15', 'unique:users,mobile'],
        ]);

        if ($validator->fails()) {
            return response()->json(['res' => false, 'msg' => $validator->errors()->first(), 'data' => '']);
        }

        $email = $request->input('email');
        $mobile = $request->input('mobile');

        $existingVendor = null;
        if (!empty($email)) {
            $existingVendor = User::where('email', $email)->first();
        } elseif (!empty($mobile)) {
            $existingVendor = User::where('mobile', $mobile)->first();
        }

        if ($existingVendor) {

            $existingVendorDetail = Vendor::where('user_id', $existingVendor->id)->first();

            if ($existingVendor->user_type == 3) {
                if ($existingVendorDetail->status != 1) {
                    return response()->json(['res' => false, 'msg' => 'User is not active . Please contact the Admin', 'data' => '']);
                }
                if (!empty($email) && $existingVendor->email !== $email) {
                    $existingVendor->email = $email;
                }
                if (!empty($mobile) && $existingVendor->mobile !== $mobile) {
                    $existingVendor->mobile = $mobile;
                }

                $otp = $this->userService->generateOTP();
                $existingVendor->otp = $otp;
                $existingVendor->save();

                $data = [
                    'reg_type' => ($existingVendor->email) ? 'email' : 'mobile',
                    'customer' => $existingVendor->id,
                    'action' => 'login',
                    'user_type' => 3,
                    'otp' => $otp,
                ];

                if( !empty($email)){
                $mailSent = Helper::sendMail($existingVendor->email, $data, 'mail.otp', 'Your OTP for Verification');
                }

                if( !empty($mobile)){
                $smsMessage = "Your OTP for mobile number verification is $otp. Do not share your OTP with anyone -From Wishmytour";
                $smsSent = Helper::sendSMS($existingVendor->mobile, $smsMessage, '1707172007352799266');
                }
                return response()->json(['res' => true, 'msg' => 'OTP sent successfully', 'data' => $data]);
            } else {
                return response()->json(['res' => false, 'msg' => 'User not authenticated as a vendor', 'data' => '']);
            }
        } else {
            $otp = $this->userService->generateOTP();
            $result = $this->userService->vendorsaveOTP($email, $mobile, $otp);

            if ($result) {
                $lastInsertedId = \DB::getPdo()->lastInsertId();

                $data = [
                    'reg_type' => ($email) ? 'email' : 'mobile',
                    'customer' => $lastInsertedId,
                    'action' => 'register',
                    'user_type' => 3,
                    'otp' => $otp,
                ];
                if( !empty($email)){
                $mailSent = Helper::sendMail($email, $data, 'mail.otp', 'Your OTP for Verification');
                }
                if( !empty($mobile)){
                $smsMessage = "Your OTP for mobile number verification is $otp. Do not share your OTP with anyone -From Wishmytour";
                $smsSent = Helper::sendSMS($mobile, $smsMessage, '1707172007352799266');
                }

                return response()->json(['res' => true, 'msg' => 'OTP sent successfully', 'data' => $data]);
            } else {
                return response()->json(['res' => false, 'msg' => 'Failed to send OTP', 'data' => '']);
            }
        }
    }


    /**
     * Vendor Login Verification
     *
     * @param Request $request
     * @return void
     */
    public function vendorVerifyOTP(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'otp' => 'required|numeric',
            'customer' => 'required',
            'action' => 'required|in:login,register',
        ]);

        if ($validator->fails()) {
            return response()->json(['res' => false, 'msg' => 'Validation failed', 'errors' => $validator->errors()]);
        }

        $otp = $request->input('otp');
        $customer = $request->input('customer');
        $action = $request->input('action');

        $user = User::find($customer);

        if (!$user) {
            return response()->json(['res' => false, 'msg' => 'User not found', 'data' => '']);
        }

        $existingVendorDetail = Vendor::where('user_id', $user->id)->first();

        if ($existingVendorDetail) {
            $vendordetails = "exist";
        } else {
            $vendordetails = "notexist";
        }


        if ($user->otp != '0' && $user->otp == $otp) {
            //  $user->update(['verified' => 1]);
            if ($action == 'login') {


                Auth::loginUsingId($user->id);
                $bankAccNoExists = 0;
                if ($existingVendorDetail) {
                    $bankAccNoExists = Vendor::where('bank_account_number', $existingVendorDetail->bank_account_number)
                        ->whereNotNull('bank_account_number')
                        ->exists() ? 1 : 0;
                }
                $data = [
                    'customer_id' => $user->id,
                    'email' => !empty($user->email) ? $user->email : null,
                    'mobile' => !empty($user->mobile) ? $user->mobile : null,
                    'user_type' => 3,
                    'vendordetails' => $vendordetails,
                    'bankAccNoExists' => $bankAccNoExists,
                    
                ];

                $token = $user->createToken('auth_token');

                return response()->json(['res' => true, 'msg' => 'Successfully logged in', 'data' => $data, 'token' => $token->accessToken]);


            } elseif ($action == 'register') {

                $userData = [
                    'email' => $user->email,
                    'mobile' => $user->mobile,

                ];

                //$newUser = User::create($userData);
                $otp2 = $this->userService->generateOTP();
                //$user->update(['verified' => 1]);
                $user->update(['otp' => $otp2]);
                $user->update(['user_type' => 3]);

                if(!empty($user->email)){
                    $user->update(['email_verified' => 1]);
                }
                if(!empty($user->mobile)){
                    $user->update(['mobile_verified' => 1]);
                }
                $data = [
                    'customer_id' => $user->id,
                    'email' => !empty($user->email) ? $user->email : null,
                    'mobile' => !empty($user->mobile) ? $user->mobile : null,
                    'user_type' => 3,
                    'vendordetails' => $vendordetails,
                    'otp' => $otp,

                ];
                $token = $user->createToken('auth_token');
                Helper::sendNotification(34, "A new Vendor has been registered");
                /// dd($token);
                return response()->json(['res' => true, 'msg' => 'Thank you for your patience', 'data' => $data, 'token' => $token->accessToken]);
            }
        } else {
            return response()->json(['res' => false, 'msg' => 'Provided confirmation code is not valid', 'data' => []]);
        }
    }

    /**
   * Add Vendor Details
    *
    * @param Request $request The request object containing vendor details.
    * @return \Illuminate\Http\JsonResponse The response indicating success or failure of the operation.
    */
    public function vendorAddDetails(Request $request)
    {
    $user = Auth::user();
    $existingVendorDetail = Vendor::where('user_id', $user->id)->first();

    if ($existingVendorDetail) {
    return response()->json(['res' => false, 'msg' => 'Vendor details already exist for this user']);
    }

    $validator = Validator::make($request->all(), [
    'name' => 'required|string|max:255',
    'have_gst' => 'required|boolean',
    'gst_number' => $request->input('have_gst') ? ['required', 'string', new GSTValidation, 'unique:vendors,gst_number'] : 'nullable|string',
    'pan_number' => ['required', 'string', new PANValidation, new UniquePanNumber('pan_number')],
    'gst_certificate_file' => 'nullable|file|max:2048|mimes:pdf,jpg,jpeg,png,webp,avif',
    'pan_card_file' => 'nullable|file|max:2048|mimes:pdf,jpg,jpeg,png,webp,avif',
    'photo' => 'nullable|file|max:2048|mimes:jpg,jpeg,png,webp,avif',
    'gst_rate' => 'nullable|numeric',
    'tcs_rate' => 'nullable|numeric',
    'organization_name' => 'nullable|string|max:255',
    'organization_type' => ['nullable', 'numeric', 'in:1,2,3'],
    'address' => 'nullable|string',
    'zip_code' => 'nullable|string|max:255',
    'city' => 'nullable|string|max:255',
    'state' => 'nullable|string|max:255',
    'country' => 'nullable|string|max:255',
    'bank_account_number' => 'nullable|string|max:255',
    'ifsc_code' => 'nullable|string|max:255',
    'bank_name' => 'nullable|string|max:255',
    'branch_name' => 'nullable|string|max:255',
    'contact_person_name' => 'nullable|string|max:255',
    'email' => 'nullable|email|max:255',
    'mobile' => 'nullable|string|max:15',
    'authorization_file' => 'required|file|max:2048|mimes:pdf,jpg,jpeg,png,webp,avif',
    'organization_pan_number' => ['required', 'string', new PANValidation, new UniquePanNumber('organization_pan_number')],
    'organization_pan_file' => 'required|file|max:2048',
    'vendor_partners.*.pan_number' => ['required_with:vendor_partners', 'string', new PANValidation, new UniquePanNumber('pan_number')],
    'vendor_directors.*.pan_number' => ['required_with:vendor_directors', 'string', new PANValidation, new UniquePanNumber('pan_number')],
    ],
    [
    'vendor_partners.*.pan_number.required_with' => 'The PAN number for partner :index is required.',
    'vendor_partners.*.pan_number.string' => 'The PAN number for partner :index must be a string.',
    'vendor_partners.*.pan_number.unique' => 'The PAN number for partner :index has already been taken.',
    'vendor_partners.*.pan_number' => 'The PAN number for partner :index is invalid.',
    // Add custom error messages for other fields as needed
    'vendor_directors.*.pan_number.required_with' => 'The PAN number for director :index is required.',
    'vendor_directors.*.pan_number.string' => 'The PAN number for director :index must be a string.',
    'vendor_directors.*.pan_number.unique' => 'The PAN number for director :index has already been taken.',
    'vendor_directors.*.pan_number' => 'The PAN number for director :index is invalid.',
    // Add custom error messages for other fields as needed
    ]);

    if ($validator->fails()) {
    return response()->json(['res' => false, 'msg' => $validator->errors()->first(), 'data' => ''], 200);
    }

    if ($user && $user->user_type == 3) {
    $result = $this->userService->vendorAddDetails($request);

    if ($result['res']) {
    return response()->json(['res' => true, 'msg' => 'Vendor information added successfully', 'data' => $result]);
    } else {
    return response()->json(['res' => false, 'msg' => $result['msg'], 'data' => '']);
    }
    } else {
    return response()->json(['res' => false, 'msg' => 'User not authenticated as a vendor', 'data' => '']);
    }
    }


    /**
     * Update Vendor Details
     *
     * @param Request $request The request object containing the updated vendor details.
     * @return \Illuminate\Http\JsonResponse The response indicating the success or failure of the update operation.
     */
    public function vendorUpdateDetails(Request $request)
    {
        $user = Auth::user();
        if (!$user || $user->user_type != 3) {
            return response()->json([
                'res' => false,
                'msg' => 'Access denied: User is not authorized.',
                'data' => null
            ], 403);
        }

        $result = $this->userService->vendorUpdateDetails($request);

        return response()->json($result, $result['res'] ? 200 : 500);
    }

    /**
     * View Vendor Profile
     *
     * @return \Illuminate\Http\JsonResponse The response containing the vendor profile information or an error message.
     */

    public function vendorViewProfile()
    {
        $user = Auth::user();
        if (!$user || $user->user_type != 3) {
            return response()->json([
                'res' => false,
                'msg' => 'Access denied: User is not authorized.',
                'data' => null
            ], 403);
        }

        $vendorDetails = Vendor::where('user_id', $user->id)
            ->first();
        if ($vendorDetails) {
            $organizationType = "";
            switch ($vendorDetails->organization_type) {
                case 1:
                    $organizationType = "Proprietor";
                    break;
                case 2:
                    $organizationType = "Partnership";
                    break;
                case 3:
                    $organizationType = "Private Limited";
                    break;
                default:
                    $organizationType = "Unknown";
                    break;
            }
            $vendorDetails->details = VendorDetail::where('vendor_id', $vendorDetails->id)->get();


            return response()->json([
                'res' => true,
                'msg' => 'Vendor information retrieved successfully',
                'user' => $user,
                'data' => $vendorDetails,
                'organization_type' => $organizationType,
                //'cities' => $cities,
            ]);
        } else {
            return response()->json([
                'res' => true,
                'msg' => 'Vendor details not found',
                'data' => null
            ], 200);
        }

    }
    /**
     * Manage Vendor Files
     *
     * @return \Illuminate\Http\JsonResponse The response containing the vendor file information or an error message.
     */

    public function vendorFileManager()
    {
        $user = Auth::user();
        if (!$user || $user->user_type != 3) {
            return response()->json([
                'res' => false,
                'msg' => 'Access denied: User is not authorized.',
                'data' => null
            ], 403);
        }
    
        $vendorDetails = Vendor::where('user_id', $user->id)->first();
        if ($vendorDetails) {
            $files = [
                ['name' => 'GST Certificate', 'file' => $vendorDetails->gst_certificate_file],
                ['name' => 'Pancard', 'file' => $vendorDetails->pan_card_file],
                ['name' => 'Cancelled Cheque', 'file' => $vendorDetails->cancelled_cheque],
                ['name' => 'Bank Authorization Letter', 'file' => $vendorDetails->authorization_letter],
                ['name' => 'Authorization Letter', 'file' => $vendorDetails->authorization_file],
            ];
    
            return response()->json([
                'res' => true,
                'msg' => 'Vendor information retrieved successfully',
                'data' => $files,
            ]);
        } else {
            return response()->json([
                'res' => true,
                'msg' => 'Vendor details not found',
                'data' => [],
            ], 200);
        }
    }


    /**
     * Edit the profile of a vendor user.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function vendorUpdateProfile(Request $request): JsonResponse
    {
        $user = Auth::user();
        if (!$user || $user->user_type != 3) {
            return response()->json([
                'res' => false,
                'msg' => 'Access denied: User is not authorized.',
                'data' => null
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $request->user()->id,
            'mobile' => 'required|string|max:20',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'res' => false,
                'msg' => $validator->errors()->first(),
                'data' => null
            ], 200);
        }

        $result = $this->userService->vendorUpdateProfile($request->all());

        return response()->json($result, $result['res'] ? 200 : 500);
    }

    /**
     * Add bank details for a vendor user.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function vendorAddBankDetails(Request $request): JsonResponse
    {
        $user = Auth::user();
        if (!$user || $user->user_type != 3) {
            return response()->json([
                'res' => false,
                'msg' => 'Access denied: User is not authorized.',
                'data' => null
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'contact_person_name' => 'nullable|string',
            'bank_account_number' => 'nullable|string',
            'ifsc_code' => 'nullable|string',
            'bank_name' => 'nullable|string',
            'branch_name' => 'nullable|string',
            'cancelled_cheque' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:5120',
           // 'authorization_letter' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'res' => false,
                'msg' => $validator->errors()->first(),
                'data' => null
            ], 200);
        }

        $result = $this->userService->vendorAddBankDetails($request);

        return response()->json($result, $result['res'] ? 200 : 500);

    }


    public function logout(Request $request)
    {
        auth()->logout();

        $request->session()->flush();

        return response()->json(['msg' => 'Successfully logged out']);
    }

    //Admin Login
    public function adminLogin(Request $request)
    {

        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);


        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {

            return response()->json(['error' => 'Invalid credentials'], 401);
        }


        if ($user->user_type !== 1) {

            return response()->json(['error' => 'Unauthorized'], 401);
        }


        $token = $user->createToken('auth_token');
        return response()->json(['res' => true, 'msg' => 'Successfully logged in', 'data' => $user, 'token' => $token->accessToken], 200);
    }


    //Notification

    public function notificationlist(Request $request)
    {
    $user = Auth::user();
    $receiverId = $user->id;

    // Check if the user is a vendor
    $vendorDetail = Vendor::where('user_id', $receiverId)->first();
    $customerDetail = CustomerDetail::where('user_id', $receiverId)->first();
    $customerDetail = CustomerDetail::where('user_id', $receiverId)->first();
    $agentDetail = AgentDetails::where('user_id', $receiverId)->first();
    if ($vendorDetail) {
        // Get notifications and vendor verification status
        $notifications = Notification::getByReceiverId($receiverId);
        $isVerified = $vendorDetail->is_verified;
    } elseif($customerDetail){
        $notifications = Notification::getByReceiverId($receiverId);
        $isVerified = null; // No vendor verification status for customers
    } elseif($agentDetail){   
        $notifications = Notification::getByReceiverId($receiverId);
        $isVerified = $agentDetail->is_verified;
    }else {
        // Handle case where user is neither vendor nor customer
        return response()->json(['message' => 'User not found'], 404);
    }

    $unreadCount = Notification::unreadNotificationsCount($receiverId);

    return response()->json([
        'notifications' => $notifications,
        'unread_count' => $unreadCount,
        'is_verified' => $isVerified
    ]);
    }

    public function markAsRead(Notification $notification)
    {
        $notification->markAsRead();

        return response()->json(['message' => 'Notification marked as read']);
    }

    public function markAllAsRead()
    {
        $user = Auth::user();
        $receiverId = $user->id;

        Notification::markAllAsRead($receiverId);

        return response()->json(['message' => 'All notifications marked as read']);
    }

    public function markAsUnread(Notification $notification)
    {
        $notification->markAsUnread();

        return response()->json(['message' => 'Notification marked as unread']);
    }

    public function removeAllNotifications()
    {
        try {
            $userId = auth()->id();
            // Delete all notifications for the logged-in user
            Notification::where('receiver_id', $userId)->delete();
            // return response()->json(['success' => true]);
            return response()->json(['msg' => 'All Notifications removed Successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }



    //**********************************************Agent Section***************************************** */


    /**
     * Agent Login
     *
     * @param Request $request
     * @return JsonResponse
     *
     */
    public function agentLogin(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required_without:mobile|string',
            'mobile' => 'required_without:email|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['res' => false, 'msg' => $validator->errors()->first(), 'data' => '']);
        }

        $email = $request->input('email');
        $mobile = $request->input('mobile');

        $existingAgent = null;
        if (!empty($email)) {
            $existingAgent = User::where('email', $email)->first();
        } elseif (!empty($mobile)) {
            $existingAgent = User::where('mobile', $mobile)->first();
        }

        if ($existingAgent) {
            if ($existingAgent->user_type == 4) {
                if (!empty($email) && $existingAgent->email !== $email) {
                    $existingAgent->email = $email;
                }
                if (!empty($mobile) && $existingAgent->mobile !== $mobile) {
                    $existingAgent->mobile = $mobile;
                }

                $otp = $this->userService->generateOTP();
                $existingAgent->otp = $otp;
                $existingAgent->save();

                $data = [
                    'reg_type' => ($existingAgent->email) ? 'email' : 'mobile',
                    'agent' => $existingAgent->id,
                    'action' => 'login',
                    'user_type' => 4,
                    'otp' => $otp,
                ];
                if (!empty($email)) {
                $mailSent = Helper::sendMail($existingAgent->email, $data, 'mail.otp', 'Your OTP for Verification');
                }
                if (!empty($mobile)) {
                $smsMessage = "Your OTP for mobile number verification is $otp. Do not share your OTP with anyone -From Wishmytour";
                $smsSent = Helper::sendSMS($existingAgent->mobile, $smsMessage, '1707172007352799266');
                }
                return response()->json(['res' => true, 'msg' => 'OTP sent successfully', 'data' => $data]);
            } else {
                $message = !empty($email) ? 'Email' : 'Mobile number';
                $message .= ' is already taken.';
                return response()->json(['res' => false, 'msg' => $message, 'data' => '']);
            }
        } else {
            $otp = $this->userService->generateOTP();
            $result = $this->userService->agentsaveOTP($email, $mobile, $otp);

            if ($result) {
                $lastInsertedId = \DB::getPdo()->lastInsertId();

                $data = [
                    'reg_type' => ($email) ? 'email' : 'mobile',
                    'agent' => $lastInsertedId,
                    'action' => 'register',
                    'user_type' => 4,
                    'otp' => $otp,
                ];

                if (!empty($email)) {
                $mailSent = Helper::sendMail($email, $data, 'mail.otp', 'Your OTP for Verification');
                }
                if (!empty($mobile)) {
                $smsMessage = "Your OTP for mobile number verification is $otp. Do not share your OTP with anyone -From Wishmytour";
                $smsSent = Helper::sendSMS($mobile, $smsMessage, '1707172007352799266');
                }
                return response()->json(['res' => true, 'msg' => 'OTP sent successfully', 'data' => $data]);
            } else {
                return response()->json(['res' => false, 'msg' => 'Failed to send OTP', 'data' => '']);
            }
        }
    }


    /**
     * Agent Login Verification
     *
     * @param Request $request
     *
     */
    public function agentVerifyOTP(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'otp' => 'required|numeric',
            'agent' => 'required',
            'action' => 'required|in:login,register',
        ]);

        if ($validator->fails()) {
            return response()->json(['res' => false, 'msg' => 'Validation failed', 'errors' => $validator->errors()]);
        }

        $otp = $request->input('otp');
        $agent = $request->input('agent');
        $action = $request->input('action');

        $user = User::find($agent);

        if (!$user) {
            return response()->json(['res' => false, 'msg' => 'User not found', 'data' => '']);
        }

        $existingAgentDetail = AgentDetails::where('user_id', $user->id)->first();

        if ($existingAgentDetail) {
            $agentDetails = "exist";
            $agentFname = $existingAgentDetail->first_name;
            $agentLname = $existingAgentDetail->last_name;
            $agentCode = $existingAgentDetail->agent_code;
        } else {
            $agentDetails = "notexist";
            $agentFname = null;
            $agentLname = null;
            $agentCode = null;
        }


        if ($user->otp != '0' && $user->otp == $otp) {
            //  $user->update(['verified' => 1]);
            if ($action == 'login') {


                Auth::loginUsingId($user->id);
                $bankAccNoExists = 0;
                if ($existingAgentDetail) {
                    $bankAccNoExists = AgentDetails::where('bank_acc_no', $existingAgentDetail->bank_acc_no)
                        ->whereNotNull('bank_acc_no')
                        ->exists() ? 1 : 0;
                }
                $data = [
                    'agent_id' => $user->id,
                    'email' => !empty($user->email) ? $user->email : null,
                    'mobile' => !empty($user->mobile) ? $user->mobile : null,
                    'user_type' => 4,
                    'agentDetails' => $agentDetails,
                    'agentFirstName' => $agentFname,
                    'agentLastName' => $agentLname,
                    'agentCode' => $agentCode,
                    'bankAccNoExists' => $bankAccNoExists,
                ];

                $token = $user->createToken('auth_token');

                return response()->json(['res' => true, 'msg' => 'Successfully logged in', 'data' => $data, 'token' => $token->accessToken]);


            } elseif ($action == 'register') {

                $userData = [
                    'email' => $user->email,
                    'mobile' => $user->mobile,

                ];

                //$newUser = User::create($userData);
                $otp2 = $this->userService->generateOTP();
                //$user->update(['verified' => 1]);
                $user->update(['otp' => $otp2]);
                $user->update(['user_type' => 4]);

                $data = [
                    'customer_id' => $user->id,
                    'email' => !empty($user->email) ? $user->email : null,
                    'mobile' => !empty($user->mobile) ? $user->mobile : null,
                    'user_type' => 4,
                    'agentFirstName' => $agentFname,
                    'agentLastName' => $agentLname,
                    'agentDetails' => $agentDetails

                ];
                $token = $user->createToken('auth_token');
               // Helper::sendNotification(34, "A new Agent has been registered");
                /// dd($token);
                return response()->json(['res' => true, 'msg' => 'Thank you for your patience', 'data' => $data, 'token' => $token->accessToken]);
            }
        } else {
            return response()->json(['res' => false, 'msg' => 'Provided confirmation code is not valid', 'data' => []]);
        }
    }

    /**
     * Register Details by agent
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function addAgent(Request $request): JsonResponse
    {
        // Validate the request data for both agent details and banking account details
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => 'required|email',
            'mobile' => 'required|string|max:20',
            'address' => 'nullable|string',
           // 'pan_number' => ['required', 'string', new PANValidation, 'unique:agent_details,pan_number'],
            'pan_number' => ['required', 'string', new PANValidation, new UniquePanNumber('pan_number')],
            'pan_card_file' => 'nullable|file|max:2048|mimes:pdf,jpg,jpeg,png,webp,avif',
            'profile_img' => 'nullable|file|max:2048|mimes:jpg,jpeg,png,webp,avif',
            'bank_person_name' => 'nullable|string',
            'bank_acc_no' => 'nullable|string',
            'ifsc_code' => 'nullable|string',
            'bank_name' => 'nullable|string',
            'branch_name' => 'nullable|string',
           // 'authorization_file' => 'required|file|max:2048|mimes:pdf,jpg,jpeg,png,webp,avif',
            'cancelled_cheque' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:2048',
           // 'authorization_letter' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:2048',
        ]);

        // Handle validation errors
        if ($validator->fails()) {
            return response()->json(['res' => false, 'msg' => $validator->errors()->first(), 'data' => '']);
        }

        // Extract validated data
        $userData = $request->only([
            'user_id', 'first_name', 'last_name', 'email', 'mobile', 'address', 'pan_number',
            'bank_person_name', 'bank_acc_no', 'ifsc_code', 'bank_name', 'branch_name'
        ]);

        // Find the user by ID
        $user = User::find($userData['user_id']);

        // Check if the user exists and is of the correct user_type
        if (!$user || $user->user_type != User::ROLE_AGENT) {
            return response()->json(['res' => false, 'msg' => 'Invalid user or user_type', 'data' => '']);
        }

        // Check if the provided email exists and belongs to the user
        if ($user->email !== $userData['email']) {
            $emailExists = User::where('email', $userData['email'])->exists();
            if ($emailExists) {
                return response()->json(['res' => false, 'msg' => 'The email is already in use by another user', 'data' => '']);
            }
        }

        // Check if agent details already exist for the user
        $existingAgentDetail = AgentDetails::where('user_id', $userData['user_id'])->first();
        if ($existingAgentDetail) {
            return response()->json(['res' => false, 'msg' => 'Agent details already exist for this user_id', 'data' => '']);
        }

        // Update user's email and mobile if necessary
        $user->update([
            'email' => $userData['email'],
            'mobile' => $userData['mobile'],
        ]);

        // Create new agent details
        $agentDetails = new AgentDetails();
        $agentDetails->fill($userData);
        $agentDetails->bank_verified = 1; // Set bank_verified to 1
        $agentDetails->is_verified = 1; // Set is_verified to 1

        // Get the agent file directory
        $agentFileDirectory = $this->userService->getAgentFileDirectory();

        // Handle PAN card file upload
        if ($request->hasFile('pan_card_file')) {
            $pancardImage = $request->file('pan_card_file');
            if ($pancardImage->isValid()) {
                $filename = 'pan_' . uniqid() . '.' . $pancardImage->getClientOriginalExtension();
                $directory = $this->userService->getAgentUploadDirectory();
                $pancardImage->storeAs($directory, $filename);
                $agentDetails->pan_card_file = $agentFileDirectory . $filename;
            } else {
                return response()->json(['res' => false, 'msg' => 'Invalid PAN card image file', 'data' => '']);
            }
        }

        // Handle profile image upload
        if ($request->hasFile('profile_img')) {
            $profileImg = $request->file('profile_img');
            if ($profileImg->isValid()) {
                $filename = 'profile_img_' . uniqid() . '.' . $profileImg->getClientOriginalExtension();
                $directory = $this->userService->getAgentUploadDirectory();
                $profileImg->storeAs($directory, $filename);
                $agentDetails->profile_img = $agentFileDirectory . $filename;
            } else {
                return response()->json(['res' => false, 'msg' => 'Invalid profile image file', 'data' => '']);
            }
        }

        // Handle cancelled cheque upload
        if ($request->hasFile('cancelled_cheque')) {
            $cancelledCheque = $request->file('cancelled_cheque');
            if ($cancelledCheque->isValid()) {
                $filename = 'cancelled_cheque_' . uniqid() . '.' . $cancelledCheque->getClientOriginalExtension();
                $directory = $this->userService->getAgentUploadDirectory();
                $cancelledCheque->storeAs($directory, $filename);
                $agentDetails->cancelled_cheque = $agentFileDirectory . $filename;
            } else {
                return response()->json(['res' => false, 'msg' => 'Invalid cancelled cheque file', 'data' => '']);
            }
        }

       

        // Save the agent details
        if ($agentDetails->save()) {
            $agentDetails->agent_code = 'WMTAG' . str_pad($agentDetails->id, 5, '0', STR_PAD_LEFT);
            $agentDetails->save();
            //SMS Approval with Agent Code
            $agentFirstName = $agentDetails->first_name;
            $agentLastName = $agentDetails->last_name;
            $agentMobile = $user->mobile;

            //SMS Approval with Agent Code
            $smsMessage = "Dear {$agentFirstName} {$agentLastName}, your registration as an agent on WishMyTour is complete. Your unique Agent Code is: {$agentDetails->agent_code}. Please keep this code safe for future transactions. Welcome aboard! -From WishMyTour";
            $smsSent = Helper::sendSMS($agentMobile, $smsMessage, '1707172173571041166');

            return response()->json(['res' => true, 'msg' => 'Agent details and banking account information saved successfully', 'data' => $agentDetails, 'user' => $user]);
        } else {
            return response()->json(['res' => false, 'msg' => 'Failed to save agent details', 'data' => '']);
        }
    }

    /**
     * Register Details view by agent
     *
     * @return mixed
     */
    public function agentDetailsView()
    {
        $user = Auth::user();

        if ($user) {

            $agentDetails = AgentDetails::where('user_id', $user->id)->first();
            $tdsRate = Config::where('name', 'tds')->value('value');
            $agentCommission = Config::where('name', 'agent_commission')->value('value');

            if ($agentDetails) {
                return response()->json(['res' => true, 'msg' => 'Agent details retrieved successfully', 'data' => $agentDetails, 'tdsRate' => $tdsRate,'agentCommission' => $agentCommission,'User' => $user]);
            } else {
                return response()->json(['res' => false, 'msg' => 'Agent details not found', 'data' => '']);
            }
        } else {
            return response()->json(['res' => false, 'msg' => 'User not authenticated', 'data' => '']);
        }
    }

    /**
     * Register Details Edit by Agent
     *
     * @param Request $request
     *
     */
    public function editAgent(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',

        ]);

        if ($validator->fails()) {
            return response()->json(['res' => false, 'msg' => $validator->errors()->first(), 'data' => '']);
        }

        $user = Auth::user();

        if ($user) {

            $agentDetails = AgentDetails::where('user_id', $user->id)->first();

            if ($agentDetails) {

                $agentDetails->update($request->all());
                $user->email = $request->input('email');
                $user->mobile = $request->input('mobile');
                $user->save();


                //  dd(  $user);
                return response()->json(['res' => true, 'msg' => 'Agent details updated successfully', 'data' => $agentDetails, 'user' => $user]);
            } else {
                return response()->json(['res' => false, 'msg' => 'Agent details not found', 'data' => '']);
            }
        } else {
            return response()->json(['res' => false, 'msg' => 'User not authenticated', 'data' => '']);
        }
    }

    public function addBankingAccountAgent(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'bank_person_name' => 'nullable|string',
            'bank_acc_no' => 'nullable|string',
            'ifsc_code' => 'nullable|string',
            'bank_name' => 'nullable|string',
            'branch_name' => 'nullable|string',
            'cancelled_cheque' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:2048',
          //  'authorization_letter' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['res' => false, 'msg' => $validator->errors()->first(), 'data' => ''], 200);
        }

        $user = Auth::user();

        if ($user && $user->user_type == 4) {
            $result = $this->userService->addAgentBankingAccDetails($user, $request);

            if ($result['success']) {
                return response()->json(['res' => true, 'msg' => 'Banking account information added successfully', 'data' => $result['agentDetails']]);
            } else {
                return response()->json(['res' => false, 'msg' => $result['msg'], 'data' => '']);
            }
        } else {
            return response()->json(['res' => false, 'msg' => 'User not authenticated as a vendor', 'data' => '']);
        }
    }
    /**
     * Retrieve GST Details
     *
    * @param \Illuminate\Http\Request $request The incoming request instance.
     * @param string $gstin The GSTIN to retrieve details for.
     * @return \Illuminate\Http\JsonResponse|null The response containing GST details or an error message.
     */


    public function getGstDetails(Request $request, string $gstin): ?array
    {
        // Validate the gstin parameter
        $validator = Validator::make(['gstin' => $gstin], [
            'gstin' => 'required|string|size:15', // Adjust the validation rules as needed
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => true, 'message' => 'Invalid GSTIN'], 400);
        }

        $apiUrl = 'https://apisetu.gov.in/gstn/v2/taxpayers/';
        $clientId = env('APISETU_CLIENTID');
        $apiKey = env('APISETU_APIKEY');
        $url = $apiUrl . $gstin;

        // Initialize cURL session
        $ch = curl_init();

        // Set cURL options
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                'X-APISETU-CLIENTID:' . $clientId,
                'X-APISETU-APIKEY:' . $apiKey,
            ],
        ]);

        // Execute the cURL request
        $response = curl_exec($ch);

        // Check for cURL errors and response
        if (curl_errno($ch) || !$response) {
            $errorMessage = curl_error($ch) ?: 'No response from server';
            curl_close($ch);
            return ['error' => true, 'message' => $errorMessage];
        }

        // Close the cURL session
        curl_close($ch);

        // Decode the JSON response
        $responseArray = json_decode($response, true);

        // Return the response array
        return $responseArray;
    }
    /**
     * Retrieve PAN Details
     *
     * @param \Illuminate\Http\Request $request The incoming request instance.
     * @param string $pan The PAN to retrieve details for.
     * @return \Illuminate\Http\JsonResponse|null The response containing PAN details or an error message.
     */
    public function getPanDetails(Request $request, string $pan): ?array
    {
       
        // Validate the pan parameter
        $validator = Validator::make(['pan' => $pan], [
            'pan' => 'required|string|size:10|alpha_num', // PAN is typically 10 alphanumeric characters
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => true, 'message' => 'Invalid PAN'], 400);
        }

        $apiUrl = 'https://apisetu.gov.in/certificate/v3/pan/pancr';
        $clientId = env('APISETU_CLIENTID');
        $apiKey = env('APISETU_APIKEY');
        $url = $apiUrl . $pan;

        // Initialize cURL session
        $ch = curl_init();

        // Set cURL options
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                'X-APISETU-CLIENTID:' . $clientId,
                'X-APISETU-APIKEY:' . $apiKey,
            ],
        ]);

        // Execute the cURL request
        $response = curl_exec($ch);

        // Check for cURL errors and response
        if (curl_errno($ch) || !$response) {
            $errorMessage = curl_error($ch) ?: 'No response from server';
            curl_close($ch);
            return ['error' => true, 'message' => $errorMessage];
        }

        // Close the cURL session
        curl_close($ch);

        // Decode the JSON response
        $responseArray = json_decode($response, true);

        // Return the response array
        return $responseArray;
    }
    /**
     * Verify Email or Mobile
     *
     * @param \Illuminate\Http\Request $request The incoming request instance.
     * @return \Illuminate\Http\JsonResponse The response indicating the result of the verification.
     */

    public function verifyEmailOrMobile(Request $request)
   {
    $validator = Validator::make($request->all(), [
        'email' => 'nullable|email',
        'mobile' => 'nullable|string|max:15',
        'otp' => 'required|numeric',
        'user_id' => 'required|exists:users,id',
    ]);

    if ($validator->fails()) {
        return response()->json(['res' => false, 'msg' => 'Validation failed', 'errors' => $validator->errors()]);
    }

    $email = $request->input('email');
    $mobile = $request->input('mobile');
    $otp = $request->input('otp');
    $userId = $request->input('user_id');

    // Find the user based on the provided user_id
    $user = User::where('id', $userId)
                ->where(function($query) use ($email, $mobile) {
                    $query->where('email', $email)
                          ->orWhere('mobile', $mobile);
                })
                ->whereNotNull('email_verified')
                ->whereNotNull('mobile_verified')
                ->first();

    if (!$user) {
        return response()->json(['res' => false, 'msg' => 'User not found']);
    }

    // Verify OTP
    if ($user->otp != $otp) {
        return response()->json(['res' => false, 'msg' => 'Invalid OTP']);
    }

    // Update email_verified or mobile_verified based on the provided input
    if ($email && $user->email_verified == 0) {
        $user->update(['email_verified' => 1]);
    } elseif ($mobile && $user->mobile_verified == 0) {
        $user->update(['mobile_verified' => 1]);
    }

    return response()->json(['res' => true, 'msg' => 'Verification successful']);
    }

    /**
     * Update OTP for a user and send it via SMS or email
     * @param \Illuminate\Http\Request $request The incoming request instance containing user ID, email, and mobile.
     * @return \Illuminate\Http\JsonResponse The response indicating the success or failure of the OTP update and sending.
     */

    public function updateOTP(Request $request)
    {
    $validator = Validator::make($request->all(), [
    'user_id' => 'required|exists:users,id',
    'email' => 'nullable|email',
    'mobile' => 'nullable|string|max:15',
    ]);

    if ($validator->fails()) {
    return response()->json(['res' => false, 'msg' => 'Validation failed', 'errors' => $validator->errors()]);
    }

    $user_id = $request->input('user_id');
    $email = $request->input('email');
    $mobile = $request->input('mobile');

    // Find the user by user_id
    $user = User::find($user_id);

    if (!$user) {
    return response()->json(['res' => false, 'msg' => 'User not found']);
    }

    // Check for duplicate email
    if ($email && User::where('email', $email)->where('id', '!=', $user_id)->exists()) {
    return response()->json(['res' => false, 'msg' => 'The email is already in use by another user']);
    }

    // Check for duplicate mobile
    if ($mobile && User::where('mobile', $mobile)->where('id', '!=', $user_id)->exists()) {
    return response()->json(['res' => false, 'msg' => 'The mobile number is already in use by another user']);
    }

    // Generate new OTP
    $otp = $this->userService->generateOTP();

    $updateData = ['otp' => $otp];
    $user->update($updateData);

    if (!empty($mobile)) {
    $smsMessage = "Your OTP for mobile number verification is $otp. Do not share your OTP with anyone -From Wishmytour";
    $smsSent = Helper::sendSMS($mobile, $smsMessage, '1707172007352799266');
    }

    if (!empty($email)) {
    $data = ['otp' => $otp];
    try {
    $mailSent = Helper::sendMail($email, $data, 'mail.otp', 'Your OTP for verification');
    if (!$mailSent) {
    return response()->json(['res' => false, 'msg' => 'Failed to send OTP email']);
    }
    } catch (\Exception $e) {
    return response()->json(['res' => false, 'msg' => 'Failed to send OTP email', 'error' => $e->getMessage()]);
    }
    }

    return response()->json(['res' => true, 'msg' => 'OTP updated successfully', 'otp' => $otp]);
    }
}