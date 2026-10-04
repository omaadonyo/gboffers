<?php
namespace App\Rules;
use App\Support\Phone;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
class UgPhone implements ValidationRule {
    public function validate(string $attribute, mixed $value, Closure $fail): void {
        if (!is_string($value) || !Phone::validUg($value)) { $fail('Enter a valid Ugandan phone number, e.g. 0770 123 456 or +256770123456.'); }
    }
}
