<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class PANValidation implements Rule
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
        // PAN format validation logic goes here
        // Implement the logic according to the PAN format in India
        // Example logic: Check if PAN is in the correct format (e.g., AAAAB1234C)
        return preg_match('/^[A-Z]{5}[0-9]{4}[A-Z]$/', $value);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The :attribute must be a valid PAN number.';
    }
}
