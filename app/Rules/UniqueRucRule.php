<?php

namespace App\Rules;

use App\Models\Company;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class UniqueRucRule implements ValidationRule
{
    public $user_id;
    public function __construct($user_id)
    {
        $this->user_id = $user_id;
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
            ->where('user_id', $this->user_id)
            ->first();
        
        if ($company) {
            $fail('La compañía ya existe.');
        }
    }
}
