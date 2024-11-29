<?php

namespace Modules\Frontend\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Admin\app\Models\Pages;
use Modules\Frontend\app\Models\ContactForm;
use Illuminate\Support\Facades\DB;
use App\Helpers\Helper;
class FrontendController extends Controller
{
      /**
     * Display the specified page by slug.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function cmsPages(Request $request)
    {
       
        $request->validate([
            'slug' => 'required|string'
        ]);
        $slug = $request->input('slug');
        $page = Pages::where('slug', $slug)->first();
        if (!$page) {
            return response()->json(['msg' => 'Page not found'], 404);
        }
        return response()->json(['page' => $page], 200);
    }

    /**
     * Handle contact form submission.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function submitContactForm(Request $request)
    {
        // Validate the request
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone_number' => 'required|string|max:15',
            'message' => 'required|string',
        ]);

        // Create a new contact form entry
        $contactForm = ContactForm::create([
            'first_name' => $request->input('first_name'),
            'last_name' => $request->input('last_name'),
            'email' => $request->input('email'),
            'phone_number' => $request->input('phone_number'),
            'message' => $request->input('message'),
        ]);

        // Email data
        $data = [
            'first_name' => $request->input('first_name'),
            'last_name' => $request->input('last_name'),
            'email' => $request->input('email'),
            'message' => $request->input('message'),
            'siteUrl' => config('app.url'),
            'siteName' => config('app.name'),
            'appUrl' => config('app.url')
        ];

        $mailSent = Helper::sendMail($request->input('email'), $data, 'mail.contact', 'Contact Us');
        // Return a response
        return response()->json(['res'=>true,'msg' => 'Thank you for reaching out to us! Your message has been received, and our team will get back to you shortly.', 'data' => $contactForm], 200);
    }
       /**
     * Get company details.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCompanyDetails()
    {
        $companyDetails = DB::table('configs')->whereIn('name', ['company_name', 'company_address'])->get();
//dd($companyDetails);
        if ($companyDetails->isEmpty()) {
            return response()->json(['msg' => 'Company details not found'], 404);
        }

        return response()->json(['res'=>'true' , 'data' => $companyDetails], 200);
    }
}
