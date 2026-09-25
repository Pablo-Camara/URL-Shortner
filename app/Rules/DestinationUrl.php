<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class DestinationUrl implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $parts = is_string($value) ? parse_url($value) : false;
        $host = strtolower(rtrim($parts['host'] ?? '', '.'));
        $ownHost = strtolower(rtrim(parse_url(config('app.url'), PHP_URL_HOST) ?? '', '.'));
        if (! is_string($value) || ! filter_var($value, FILTER_VALIDATE_URL)
            || ! in_array(strtolower($parts['scheme'] ?? ''), ['http', 'https'], true)
            || isset($parts['user']) || isset($parts['pass'])
            || preg_match('/[\x00-\x20\x7f\\\\]/', $value)
            || in_array($host, [$ownHost, 'localhost', '127.0.0.1', '[::1]'], true)) {
            $fail('Use a complete HTTP or HTTPS URL without credentials, spaces or a link to this service.');
        }
    }
}
