<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class GSTValidation implements Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        // GST format validation logic goes here
        // Implement the logic according to the GST format in India
        // Example logic: Check if GST is in the correct format (e.g., 22ABCDE1234F1Z5)
        return preg_match('/\d{2}[A-Z]{5}\d{4}[A-Z]{1}[A-Z\d]{1}[Z]{1}[A-Z\d]{1}/', $value);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The :attribute must be a valid GST number.';
    }
}
