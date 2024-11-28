<?php

namespace Modules\Booking\App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\JsonResponse;

class AddBookingRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'add_on_id' => 'nullable|string',
            'coupon_id' => 'nullable|string',
            'package_id' => 'required|integer|exists:packages,id',
            'base_amount' => 'required|numeric',
            'addon_total_price' => 'nullable|numeric',
            'website_price' => 'nullable|numeric',
            'website_percent' => 'nullable|numeric',
            'gst_price' => 'nullable|numeric',
            'gst_percent' => 'nullable|numeric',
            'tcs' => 'nullable|numeric',
            'coupon_price' => 'nullable|numeric',
            'final_price' => 'nullable|numeric',
            'paid_amount' => 'nullable|numeric',
            'rooms' => 'required|array',
            'rooms.*.room_no' => 'required|string|max:255',
            'rooms.*.adults' => 'required|integer|min:1',
            'rooms.*.children' => 'required|integer|min:0',
            'rooms.*.passengers' => 'required|array',
            'rooms.*.passengers.*.title' => 'nullable|string|max:10',
            'rooms.*.passengers.*.first_name' => 'required|string|max:100',
            'rooms.*.passengers.*.last_name' => 'nullable|string|max:100',
            'rooms.*.passengers.*.dob' => 'nullable|date',
            'rooms.*.passengers.*.gender' => 'nullable|in:male,female,other',
            'rooms.*.passengers.*.is_adult' => 'required|integer|in:0,1',
            'rooms.*.passengers.*.price' => 'nullable|numeric',
            'seats' => 'required|array',
            'seats.*.booking_date' => 'required|date',
            'seats.*.cost' => 'required|numeric|min:0',
            'customer_name' => 'required|string|max:100',
            'customer_address' => 'nullable|string|max:255',
            'customer_email' => 'nullable|email|max:100',
            'customer_phone_number' => 'nullable|string|max:100|regex:/^\d{10}$/',
            'customer_state_id' => 'nullable|integer|exists:states,id',
            'customer_pan_number' => 'nullable|string|max:100',
            'customer_booking_for' => 'required|integer|in:1,2',
            'agent_code' => 'nullable|exists:agent_details,agent_code',
        ];
    }

    /**
     * Create a json response on validation errors.
     *
     * @param Validator $validator
     * @return JsonResponse
     */
    public function failedValidation(Validator $validator): JsonResponse
    {
        throw new HttpResponseException(response()->json([
            'res' => false,
            'msg' => $validator->errors()->first(),
            'data' => ""
        ]));

    }
}
