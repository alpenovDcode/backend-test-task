<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class TaxNumber extends Constraint
{
    public string $message = 'The tax number "{{ value }}" is not valid.';
    public string $invalidFormatMessage = 'The tax number "{{ value }}" has invalid format for country {{ country }}.';
    public string $unsupportedCountryMessage = 'Country code "{{ country }}" is not supported.';
} 