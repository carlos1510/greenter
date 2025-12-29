<?php

namespace App\Rules;

use App\Models\Company;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class UniqueRucRule implements ValidationRule
{
    public $company_id;

    public function __construct($company_id = null)
    {
        $this->company_id = $company_id;
    }
    /**
     * Run the validation rule.
     *
     * @param  string $attribute
     * @param  mixed $value
     * @param  \Closure $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (empty($value)) {
            return;
        }

        $company = Company::where('ruc', $value)
            ->where('user_id', JWTAuth::user()->id)
            ->when($this->company_id, function($query, $company_id) {
                $query->where('id', '!=', $company_id);
            })
            ->first();
        
        if ($company) {
            $fail('La compañía ya existe.');
        }
    }
}
