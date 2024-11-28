<?php



namespace Modules\Booking\App\Http\Requests;



use Illuminate\Foundation\Http\FormRequest;

use Illuminate\Http\Exceptions\HttpResponseException;

use Illuminate\Contracts\Validation\Validator;

use Illuminate\Http\JsonResponse;



class AddOnrequestBookingRequest extends FormRequest
{
    public function authorize()
    {
        // Ensure the user is authorized to make this request
        return true;
    }

    public function rules()
    {
        return [
            'package_id' => 'required|integer|exists:packages,id',
            'token' => 'required|string',
            'rooms' => 'required|array',
            'rooms.*.room_no' => 'required|integer',
            'rooms.*.adults' => 'required|integer|min:1',
            'rooms.*.children' => 'nullable|integer|min:0',
            'status' => 'required|integer', // Example: status validation
            'add_on_id' => 'nullable|integer|exists:package_addons,id',
            'start_date' => 'required|date',
        ];
    }

    public function messages()
    {
        return [
            'package_id.required' => 'Package ID is required',
            'package_id.exists' => 'The selected package does not exist',
            'token.required' => 'Token is required',
            'rooms.required' => 'At least one room is required',
            'rooms.array' => 'Rooms must be an array',
            'rooms.*.room_no.required' => 'Room number is required for each room',
            'rooms.*.adults.required' => 'Number of adults is required for each room',
            'rooms.*.adults.min' => 'There must be at least one adult in each room',
            'rooms.*.children.min' => 'Number of children cannot be negative',
            'status.required' => 'Status is required',
            'add_on_id.exists' => 'The selected add-on does not exist',
            'start_date.required' => 'Start date is required',
            'start_date.date' => 'Start date must be a valid date',
        ];
    }
}

