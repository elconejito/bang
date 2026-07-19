<?php

namespace App\Rules;

use App\Models\Firearm;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

class ActiveFirearm implements ValidationRule
{
    public function __construct(private readonly int $userId) {}

    /**
     * Run the validation rule.
     *
     * @param  Closure(string, ?string=): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! Firearm::withoutGlobalScopes()
            ->whereKey($value)
            ->where('user_id', $this->userId)
            ->whereNull('archived_at')
            ->exists()) {
            $fail('The selected firearm must be active.');
        }
    }
}
