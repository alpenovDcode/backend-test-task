<?php

namespace App\Validator\Constraints;

use App\Service\TaxNumberValidatorService;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

class TaxNumberValidator extends ConstraintValidator
{
    private TaxNumberValidatorService $taxNumberValidator;

    public function __construct(TaxNumberValidatorService $taxNumberValidator)
    {
        $this->taxNumberValidator = $taxNumberValidator;
    }

    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof TaxNumber) {
            throw new UnexpectedTypeException($constraint, TaxNumber::class);
        }

        if (null === $value || '' === $value) {
            return;
        }

        if (!is_string($value)) {
            throw new UnexpectedValueException($value, 'string');
        }

        if (strlen($value) < 2) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ value }}', $value)
                ->addViolation();
            return;
        }

        $countryCode = substr($value, 0, 2);
        if (!in_array($countryCode, ['DE', 'IT', 'GR', 'FR'])) {
            $this->context->buildViolation($constraint->unsupportedCountryMessage)
                ->setParameter('{{ country }}', $countryCode)
                ->setParameter('{{ value }}', $value)
                ->addViolation();
            return;
        }

        if (!$this->taxNumberValidator->validate($value)) {
            $this->context->buildViolation($constraint->invalidFormatMessage)
                ->setParameter('{{ country }}', $countryCode)
                ->setParameter('{{ value }}', $value)
                ->addViolation();
        }
    }
} 