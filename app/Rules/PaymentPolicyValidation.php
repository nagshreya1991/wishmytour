<?php

namespace App\Rules;


use Illuminate\Contracts\Validation\Rule;

class PaymentPolicyValidation implements Rule
{
    public function passes($attribute, $value)
    {
        // Check if value is an array
        if (!is_array($value)) {
            return false;
        }

        // Check if the last policy has a 100% cancellation fee
        $lastPolicy = end($value);
        $lastPolicyParts = explode('-', $lastPolicy);
        if ((int)$lastPolicyParts[2] !== 100) {
            return false;
        }

//        // Initialize previous values
//        $prevDaysFrom = PHP_INT_MAX;
//        $prevDaysTo = PHP_INT_MAX;
//        $prevPercent = 0;
//
//        foreach ($value as $policy) {
//            $parts = explode('-', $policy);
//            if (count($parts) !== 3) {
//                return false; // Invalid format
//            }
//
//            $daysFrom = (int)$parts[0];
//            $daysTo = (int)$parts[1];
//            $percent = (int)$parts[2];
//
//            // Check if days are in decreasing order
//            if ($daysFrom >= $prevDaysFrom || $daysTo >= $prevDaysTo) {
//                return false;
//            }
//
//            // Check if percentages are in increasing order
//            if ($percent <= $prevPercent) {
//                return false;
//            }
//
//            // Update previous values
//            $prevDaysFrom = $daysFrom;
//            $prevDaysTo = $daysTo;
//            $prevPercent = $percent;
//        }

        return true;
    }

    public function message()
    {
        return 'The payment policy is invalid. The last policy has a 100% fee.';
    }
}
