<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\DB;
class UniquePanNumber implements Rule
{
    protected $column;

    public function __construct($column)
    {
        $this->column = $column;
    }

    public function passes($attribute, $value)
    {
        $vendorPanExists = DB::table('vendors')->where('pan_number', $value)->exists();
        $vendorOrgPanExists = DB::table('vendors')->where('organization_pan_number', $value)->exists();
        $agentPanExists = DB::table('agent_details')->where('pan_number', $value)->exists();

        return !$vendorPanExists && !$vendorOrgPanExists && !$agentPanExists;
    }

    public function message()
    {
        return 'The :attribute already exists in the system.';
    }
}
