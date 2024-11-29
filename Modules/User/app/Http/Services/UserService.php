<?php

namespace Modules\User\App\Http\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Modules\User\app\Models\User;
use Illuminate\Support\Facades\Validator;
use Modules\User\app\Models\CustomerDetail;
use Modules\User\app\Models\Vendor;
use Modules\User\app\Models\VendorDetail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;
use App\Helpers\Helper;
use Modules\User\app\Models\AgentDetails;
use Exception;


class UserService
{
    protected User $user;
    protected $agentUploadDirectory;
    protected $agentFileDirectory;
   

    public function __construct()
    {
        // Define the upload directory path with user_id
        $this->vendorUploadDirectory = 'public/vendors/';
        $this->agentUploadDirectory = 'public/agent_files/';
    }

    public function generateOTP()
    {
        $otp = rand(1000, 9999);
        return strval($otp);
    }

    public function saveOTP($login, $otp)
    {
        $userData = [
            'otp' => $otp,
            'user_type' => 2,
        ];

        // Determine if $login is an email or mobile
        if (filter_var($login, FILTER_VALIDATE_EMAIL)) {
            // If $login is a valid email address
            $userData['email'] = $login;
        } else {
            // If $login is not a valid email, assume it's a mobile number
            $userData['mobile'] = $login;
        }

        $result = User::create($userData);

        return $result;
    }


    public function addPhotoId($userId, $idType, $idNumber)
    {
        // Fetch and update customer details with ID proof information
        $customerDetails = CustomerDetail::where('user_id', $userId)->first();

        if ($customerDetails) {
            $customerDetails->update([
                'id_type' => $idType,
                'id_number' => $idNumber,
                //  'id_verified' => $idVerified,
            ]);

            return $customerDetails;
        }

        return null;
    }

    public function verifyPhoneOTP(User $user, $otp)
    {
        // Implement OTP verification logic here
        return $user->otp === $otp;
    }

    //******VENDOR SECTION*************/

    public function vendorsaveOTP($email = null, $mobile = null, $otp)
    {

        $email = $email ?? null;

        $mobile = $mobile ?? null;

        $result = User::create([
            'email' => $email,
            'mobile' => $mobile,
            'otp' => $otp,
            'user_type' => 3,
        ]);

        return $result;
    }

    /**
     * Create vendor information for the authenticated user.
     *
     * @param $request
     * @return array
     */
    public function vendorAddDetails($request): array
    {
        $user = Auth::user();
        $existingVendorDetail = Vendor::where('user_id', $user->id)->first();

        if ($existingVendorDetail) {
            return ['res' => false, 'msg' => 'Vendor details already exist for this user'];
        }
        // Update email and mobile if provided
        if ($request->has('email')) {
        $user->email = $request->email;
        }

        if ($request->has('mobile')) {
        $user->mobile = $request->mobile;
        }
        $user->save();

        
        // Create a new instance of VendorDetails
        $vendorDetails = new Vendor();

        $vendorDetails->user_id = $user->id; // Assuming $user variable holds the authenticated user
        $vendorDetails->name = $request->name;
        $vendorDetails->have_gst = $request->have_gst;
        $vendorDetails->gst_number = $request->gst_number;
        $vendorDetails->pan_number = $request->pan_number;
        $vendorDetails->gst_rate = $request->gst_rate;
        $vendorDetails->tcs_rate = $request->tcs_rate;
        $vendorDetails->organization_name = $request->organization_name;
        $vendorDetails->organization_type = $request->organization_type;
        $vendorDetails->address = $request->address;
        $vendorDetails->zip_code = $request->zip_code;
        $vendorDetails->city = $request->city;
        $vendorDetails->state = $request->state;
        $vendorDetails->country = $request->country;
        $vendorDetails->organization_pan_number = $request->organization_pan_number;

        // Handle PAN card file upload
        if ($request->hasFile('pan_card_file')) {
            $pancardImage = $request->file('pan_card_file');

            if ($pancardImage->isValid()) {
                $filename = 'pan_' . uniqid() . '.' . $pancardImage->getClientOriginalExtension();
                $directory = $this->getVendorUploadDirectory();
                $pancardImage->storeAs($directory, $filename);
                $vendorDetails->pan_card_file = $this->vendorFileDirectory . $filename;
            } else {
                return response()->json(['res' => false, 'msg' => 'Invalid PAN card image file'], 400);
            }
        }
        // Handle organization_pan_file upload
        if ($request->hasFile('organization_pan_file')) {
            $organizationPanFile = $request->file('organization_pan_file');
            if ($organizationPanFile->isValid()) {
                $filename = 'organization_pan_' . uniqid() . '.' . $organizationPanFile->getClientOriginalExtension();
                $directory = $this->getVendorUploadDirectory();
                $organizationPanFile->storeAs($directory, $filename);
                $vendorDetails->organization_pan_file = $this->vendorFileDirectory . $filename;
            } else {
                return response()->json(['res' => false, 'msg' => 'Invalid organization PAN image file'], 400);
            }
        }

        // Handle authorization_file upload
        if ($request->hasFile('authorization_file')) {
            $authorizationfile = $request->file('authorization_file');
            if ($authorizationfile->isValid()) {
                $filename = 'authorization_file_' . uniqid() . '.' . $authorizationfile->getClientOriginalExtension();
                $directory = $this->getVendorUploadDirectory();
                $authorizationfile->storeAs($directory, $filename);
                $vendorDetails->authorization_file = $this->vendorFileDirectory . $filename;
            } else {
                return response()->json(['res' => false, 'msg' => 'Invalid Authorization file'], 400);
            }
        }


        // Handle GST certificate file upload
        if ($request->hasFile('gst_certificate_file')) {
            $gstCertificateImage = $request->file('gst_certificate_file');

            // Check if the uploaded file is valid
            if ($gstCertificateImage->isValid()) {
                $filename = 'gst_' . uniqid() . '.' . $gstCertificateImage->getClientOriginalExtension();
                $directory = $this->getVendorUploadDirectory();
                $gstCertificateImage->storeAs($directory, $filename);
                $vendorDetails->gst_certificate_file = $this->vendorFileDirectory . $filename;
            } else {
                return response()->json(['res' => false, 'msg' => 'Invalid GST certificate image file'], 400);
            }
        }

        // Handle vendor's photo upload
        if ($request->hasFile('photo')) {
            $photo = $request->file('photo');

            // Check if the uploaded file is valid
            if ($photo->isValid()) {
                $filename = 'photo_' . uniqid() . '.' . $photo->getClientOriginalExtension();
                $directory = $this->getVendorUploadDirectory();
                $photo->storeAs($directory, $filename);
                $vendorDetails->photo = $this->vendorFileDirectory . $filename;
            } else {
                return response()->json(['res' => false, 'msg' => 'Invalid photo image file'], 400);
            }
        }

        // Save the vendor details
        if (!$vendorDetails->save()) {
            return ['res' => false, 'msg' => 'Failed to save vendor details'];
        }

        if ($vendorDetails->organization_type == 1) {
            // Handle vendor proprietorship
            $proprietor = new VendorDetail();

            $proprietor->vendor_id = $vendorDetails->id;
            $proprietor->name = $request->proprietor_name;
            $proprietor->phone_number = $request->proprietor_phone_number;
            $proprietor->pan_number = $request->proprietor_pan_number;
            $proprietor->address = $request->proprietor_address;

            if ($request->hasFile('proprietor_photo')) {
                $photo = $request->file('proprietor_photo');

                if ($photo->isValid()) {
                    $filename = 'proprietor_photo_' . uniqid() . '.' . $photo->getClientOriginalExtension();
                    $directory = $this->getVendorUploadDirectory();
                    $photo->storeAs($directory, $filename);
                    $proprietor->photo = $this->vendorFileDirectory . $filename;
                } else {
                    return [
                        'res' => false,
                        'msg' => 'Invalid proprietor image file.',
                        'data' => null
                    ];
                }
                $proprietor->save();
            }
        }

        if ($vendorDetails->organization_type == 2) {
            // Handle vendor partners
            $vendorPartners = $request->vendor_partners;
            if ($vendorPartners && is_array($vendorPartners)) {
                foreach ($vendorPartners as $partnerData) {
                    $partner = new VendorDetail();

                    $partner->vendor_id = $vendorDetails->id;
                    $partner->name = $partnerData['name'];
                    $partner->phone_number = $partnerData['phone_number'];
                    $partner->pan_number = $partnerData['pan_number'];
                    $partner->address = $partnerData['address'];

                    if (isset($partnerData['photo']) && $partnerData['photo']->isValid()) {
                        $photo = $partnerData['photo'];
                        $filename = 'partner_photo_' . uniqid() . '.' . $photo->getClientOriginalExtension();
                        $directory = $this->getVendorUploadDirectory();
                        $photo->storeAs($directory, $filename);
                        $partner->photo = $this->vendorFileDirectory . $filename;
                    } else {
                        return [
                            'res' => false,
                            'msg' => 'Invalid partner image file.',
                            'data' => null
                        ];
                    }
                    $partner->save();
                }
            }
        }

        if ($vendorDetails->organization_type == 3) {
            // Handle vendor directors
            $vendorDirectors = $request->vendor_directors;
            if ($vendorDirectors && is_array($vendorDirectors)) {
                foreach ($vendorDirectors as $directorData) {
                    $director = new VendorDetail();

                    $director->vendor_id = $vendorDetails->id;
                    $director->name = $directorData['name'];
                    $director->phone_number = $directorData['phone_number'];
                    $director->pan_number = $directorData['pan_number'];
                    $director->address = $directorData['address'];

                    if (isset($directorData['photo']) && $directorData['photo']->isValid()) {
                        $photo = $directorData['photo'];
                        $filename = 'director_photo_' . uniqid() . '.' . $photo->getClientOriginalExtension();
                        $directory = $this->getVendorUploadDirectory();
                        $photo->storeAs($directory, $filename);
                        $director->photo = $this->vendorFileDirectory . $filename;
                    } else {
                        return [
                            'res' => false,
                            'msg' => 'Invalid director image file.',
                            'data' => null
                        ];
                    }
                    $director->save();
                }
            }
        }

        Helper::sendNotification($user->id, "Your details have been successfully added. You will receive a notification once verified.");
        Helper::sendNotification(34, "New vendor details added by " . $vendorDetails->name . ". Verification pending.");


        // Prepare SMS message
        $smsMessage = "Dear {$vendorDetails->name}, we have received your request for registration as a tour operator on WishMyTour. Welcome aboard! -From Wishmytour.";

        // Ensure that the user's phone number exists before sending SMS
        if ($user->mobile) {
            $smsSent = Helper::sendSMS($user->mobile, $smsMessage, '1707172070083841469');
        } else {
            throw new \Exception('User phone number not found.');
        }


        return ['res' => true, 'vendorDetails' => $vendorDetails];

    }

    public function vendorUpdateDetails($request): array
    {
        $user = Auth::user();
        $vendorDetails = Vendor::where('user_id', $user->id)->first();

        if (!$vendorDetails) {
            return [
                'res' => false,
                'msg' => 'Failed to fetch details.',
                'data' => null
            ];
        }

        $vendorDetails->gst_rate = $request->gst_rate;
        $vendorDetails->tcs_rate = $request->tcs_rate;
        $vendorDetails->organization_name = $request->organization_name;
        $vendorDetails->address = $request->address;
        $vendorDetails->zip_code = $request->zip_code;
        $vendorDetails->city = $request->city;
        $vendorDetails->state = $request->state;
        $vendorDetails->country = $request->country;

        // Save the updated vendor details
        if (!$vendorDetails->save()) {
            return [
                'res' => false,
                'msg' => 'Failed to update details.',
                'data' => null
            ];
        }

        if ($vendorDetails->organization_type == 1) {
            $proprietor = VendorDetail::where('vendor_id', $vendorDetails->id)->first();

            if ($proprietor) {
                $proprietor->name = $request->proprietor_name;
                $proprietor->phone_number = $request->proprietor_phone_number;
                $proprietor->pan_number = $request->proprietor_pan_number;
                $proprietor->address = $request->proprietor_address;

                if ($request->hasFile('proprietor_photo')) {
                    $photo = $request->file('proprietor_photo');

                    if ($photo->isValid()) {
                        $filename = 'proprietor_photo_' . uniqid() . '.' . $photo->getClientOriginalExtension();
                        $directory = $this->getVendorUploadDirectory();
                        $photo->storeAs($directory, $filename);

                        // Delete existing photo if it exists
                        if ($proprietor->photo) {
                            Storage::disk('public')->delete($proprietor->photo);
                        }

                        $proprietor->photo = $this->vendorFileDirectory . $filename;
                    } else {
                        return [
                            'res' => false,
                            'msg' => 'Invalid photo image file.',
                            'data' => null
                        ];
                    }
                }

                $proprietor->save();
            }
        }

        if ($vendorDetails->organization_type == 2) {
            // Delete existing partners associated with the vendor
            $deletedPartners = VendorDetail::where('vendor_id', $vendorDetails->id)->get();

            foreach ($deletedPartners as $deletedPartner) {
                if ($deletedPartner->photo) {
                    Storage::disk('public')->delete($deletedPartner->photo);
                }
            }

            VendorDetail::where('vendor_id', $vendorDetails->id)->delete();

            $partnerDataArray = $request->vendor_partners;

            if ($partnerDataArray && is_array($partnerDataArray)) {
                foreach ($partnerDataArray as $partnerData) {
                    $partner = new VendorDetail();
                    $partner->vendor_id = $vendorDetails->id;
                    $partner->name = $partnerData['name'];
                    $partner->phone_number = $partnerData['phone_number'];
                    $partner->pan_number = $partnerData['pan_number'];
                    $partner->address = $partnerData['address'];

                    if (isset($partnerData['photo']) && $partnerData['photo']->isValid()) {
                        $photo = $partnerData['photo'];
                        $filename = 'partner_photo_' . uniqid() . '.' . $photo->getClientOriginalExtension();
                        $directory = $this->getVendorUploadDirectory();
                        $photo->storeAs($directory, $filename);
                        $partner->photo = $this->vendorFileDirectory . $filename;
                    } else {
                        return [
                            'res' => false,
                            'msg' => 'Invalid partner image file.',
                            'data' => null
                        ];
                    }
                    $partner->save();
                }
            }
        }

        if ($vendorDetails->organization_type == 3) {
            // Delete existing directors associated with the vendor
            $deletedDirectors = VendorDetail::where('vendor_id', $vendorDetails->id)->get();

            foreach ($deletedDirectors as $deletedDirector) {
                if ($deletedDirector->photo) {
                    Storage::disk('public')->delete($deletedDirector->photo);
                }
            }

            VendorDetail::where('vendor_id', $vendorDetails->id)->delete();

            $directorDataArray = $request->vendor_directors;

            if ($directorDataArray && is_array($directorDataArray)) {
                foreach ($directorDataArray as $directorData) {
                    $director = new VendorDetail();
                    $director->vendor_id = $vendorDetails->id;
                    $director->name = $directorData['name'];
                    $director->phone_number = $directorData['phone_number'];
                    $director->pan_number = $directorData['pan_number'];
                    $director->address = $directorData['address'];

                    if (isset($directorData['photo']) && $directorData['photo']->isValid()) {
                        $photo = $directorData['photo'];
                        $filename = 'director_photo_' . uniqid() . '.' . $photo->getClientOriginalExtension();
                        $directory = $this->getVendorUploadDirectory();
                        $photo->storeAs($directory, $filename);
                        $director->photo = $this->vendorFileDirectory . $filename;
                    } else {
                        return [
                            'res' => false,
                            'msg' => 'Invalid director image file.',
                            'data' => null
                        ];
                    }
                    $director->save();
                }
            }
        }


        return [
            'res' => true,
            'msg' => 'Changes saved successfully.',
            'data' => null
        ];
    }

    /**
     * Edit the profile of a vendor.
     *
     * @param array $data
     * @return array
     * @throws Exception
     */
    public function vendorUpdateProfile(array $data): array
    {
        try {
            $user = Auth::user();
            $vendor = Vendor::where('user_id', $user->id)->firstOrFail();

            $vendor->name = $data['name'];
            $vendor->save();

            $user->email = $data['email'];
            $user->mobile = $data['mobile'];
            $user->save();

            return [
                'res' => true,
                'msg' => 'Changes saved successfully.',
                'data' => $vendor
            ];
        } catch (Exception $e) {
            return [
                'res' => false,
                'msg' => $e->getMessage(),
                'data' => null
            ];
        }
    }

    /**
     * Add banking account details for a vendor.
     *
     * @param Request $request
     * @return array
     * @throws Exception
     */
    public function vendorAddBankDetails(Request $request): array
    {
        $user = Auth::user();
        $vendor = Vendor::firstOrCreate(['user_id' => $user->id]);

        // Handle the cancelled_cheque file upload
        if ($request->hasFile('cancelled_cheque')) {
            // Clear previous file if it exists
            if ($vendor->cancelled_cheque) {
                Storage::disk('public')->delete($vendor->cancelled_cheque);
                $vendor->cancelled_cheque = null; // Remove file path from the database
            }

            $cancelledChequeFile = $request->file('cancelled_cheque');

            if ($cancelledChequeFile->isValid()) {
                $filename = 'cheque_' . uniqid() . '.' . $cancelledChequeFile->getClientOriginalExtension();
                $directory = $this->getVendorUploadDirectory();
                $cancelledChequeFile->storeAs($directory, $filename);
                $vendor->cancelled_cheque = $this->vendorFileDirectory . $filename;
            } else {
                return [
                    'res' => false,
                    'msg' => 'Invalid cancelled cheque image file.',
                    'data' => null
                ];
            }
        }

        // // Handle the authorization_letter file upload
        // if ($request->hasFile('authorization_letter')) {
        //     // Clear previous file if it exists
        //     if ($vendor->authorization_letter) {
        //         Storage::disk('public')->delete($vendor->authorization_letter);
        //         $vendor->authorization_letter = null; // Remove file path from the database
        //     }

        //     $authorizationLetterFile = $request->file('authorization_letter');

        //     if ($authorizationLetterFile->isValid()) {
        //         $filename = 'auth_letter_' . uniqid() . '.' . $authorizationLetterFile->getClientOriginalExtension();
        //         $directory = $this->getVendorUploadDirectory();
        //         $authorizationLetterFile->storeAs($directory, $filename);
        //         $vendor->authorization_letter = $this->vendorFileDirectory . $filename;
        //     } else {
        //         return [
        //             'res' => false,
        //             'msg' => 'Invalid authorization letter image file.',
        //             'data' => null
        //         ];
        //     }
        // }


        $vendor->update([
            'contact_person_name' => $request->contact_person_name,
            'bank_account_number' => $request->bank_account_number,
            'ifsc_code' => $request->ifsc_code,
            'bank_name' => $request->bank_name,
            'branch_name' => $request->branch_name,
            'bank_verified' => 0,
        ]);

        return [
            'res' => true,
            'msg' => 'Bank details saved successfully.',
            'data' => $vendor
        ];
    }

    /**
     * Ensure the vendor upload directory exists. If not, create it.
     *
     * @return string The path to the created directory.
     */
    private function getVendorUploadDirectory(): string
    {
        $userId = Auth::id();
        $publicDirectory = $this->vendorUploadDirectory . $userId;
        $this->vendorFileDirectory = 'vendors/' . $userId . '/';

        // Check if the directory exists, if not, attempt to create it
        if (!Storage::disk('public')->exists($this->vendorFileDirectory)) {

            // Attempt to create the directory
            Storage::disk('public')->makeDirectory($this->vendorFileDirectory, 0755, true);

        }

        return $publicDirectory;
    }

    //Agent Section

    public function agentsaveOTP($email = null, $mobile = null, $otp)
    {

        $email = $email ?? null;

        $mobile = $mobile ?? null;

        $result = User::create([
            'email' => $email,
            'mobile' => $mobile,
            'otp' => $otp,
            'user_type' => 4,
        ]);

        return $result;
    }

    public function addAgentBankingAccDetails($user, $request)
    {
        $agentDetails = AgentDetails::firstOrCreate(['user_id' => $user->id]);

        // Handle the cancelled_cheque file upload
        if ($request->hasFile('cancelled_cheque')) {
            // Clear previous file if it exists
            if ($agentDetails->cancelled_cheque) {
                Storage::delete($agentDetails->cancelled_cheque);
                $agentDetails->cancelled_cheque = null; // Remove file path from the database
            }

            $cancelledChequeFile = $request->file('cancelled_cheque');

            if ($cancelledChequeFile->isValid()) {
                $filename = uniqid() . '_' . $agentDetails->user_id . '_' . $cancelledChequeFile->getClientOriginalName();
                $cancelledChequeFile->storeAs('public/agent_files/banking_details', $filename);
                $agentDetails->cancelled_cheque = 'agent_files/banking_details/' . $filename;
            } else {
                // If the file upload fails, return an error response
                return ['success' => false, 'msg' => 'Invalid cancelled_cheque image file'];
            }
        }

        // Handle the authorization_letter file upload
        // if ($request->hasFile('authorization_letter')) {
        //     // Clear previous file if it exists
        //     if ($agentDetails->authorization_letter) {
        //         Storage::delete($agentDetails->authorization_letter);
        //         $agentDetails->authorization_letter = null; // Remove file path from the database
        //     }

        //     $authorizationLetterFile = $request->file('authorization_letter');

        //     if ($authorizationLetterFile->isValid()) {
        //         $filename = uniqid() . '_' . $agentDetails->user_id . '_' . $authorizationLetterFile->getClientOriginalName();
        //         $authorizationLetterFile->storeAs('public/agent_files/banking_details', $filename);
        //         $agentDetails->authorization_letter = 'agent_files/banking_details/' . $filename;
        //     } else {
        //         // If the file upload fails, return an error response
        //         return ['success' => false, 'msg' => 'Invalid authorization_letter image file'];
        //     }
        // }


        $agentDetails->update([
            'bank_person_name' => $request->bank_person_name,
            'bank_acc_no' => $request->bank_acc_no,
            'ifsc_code' => $request->ifsc_code,
            'bank_name' => $request->bank_name,
            'branch_name' => $request->branch_name,
            "bank_verified" => $request->bank_verified,

        ]);

        return ['success' => true, 'agentDetails' => $agentDetails];
    }

    public function getAgentUploadDirectory(): string
    {
        $userId = Auth::id();
        $publicDirectory = $this->agentUploadDirectory . $userId;
        $this->agentFileDirectory = 'agent_files/' . $userId . '/';

        // Check if the directory exists, if not, attempt to create it
        if (!Storage::disk('public')->exists($this->agentFileDirectory)) {
            // Attempt to create the directory
            Storage::disk('public')->makeDirectory($this->agentFileDirectory, 0755, true);
        }

        return $publicDirectory;
    }

    public function getAgentFileDirectory(): string
    {
        if (!$this->agentFileDirectory) {
            $this->getAgentUploadDirectory();
        }
        return $this->agentFileDirectory;
    }
    public function isPanNumberDuplicate($panNumber): bool
    {
        $vendorPanExists = DB::table('vendors')->where('pan_number', $panNumber)->exists();
        $organizationPanExists = DB::table('vendors')->where('organization_pan_number', $panNumber)->exists();
        $agentPanExists = DB::table('agent_details')->where('pan_number', $panNumber)->exists();

        return $vendorPanExists || $organizationPanExists || $agentPanExists;
    }
}
