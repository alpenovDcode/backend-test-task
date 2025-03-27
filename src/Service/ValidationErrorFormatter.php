<?php

namespace App\Service;

use Symfony\Component\Validator\ConstraintViolationListInterface;

class ValidationErrorFormatter
{
    public function format(ConstraintViolationListInterface $violations): array
    {
        $errors = [];
        
        foreach ($violations as $violation) {
            $propertyPath = $violation->getPropertyPath();
            $errors[$propertyPath] = $violation->getMessage();
        }
        
        return [
            'errors' => $errors,
            'message' => 'Validation failed'
        ];
    }
} 