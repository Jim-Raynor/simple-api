<?php

namespace App\Validator;

use Symfony\Component\Validator\Exception\ValidationFailedException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

trait ValidatorTrait
{
    protected ValidatorInterface $validator;

    public function setValidator(ValidatorInterface $validator): void
    {
        $this->validator = $validator;
    }

    protected function validate($value, $constraints = null, $groups = null)
    {
        $errors = $this->validator->validate($value, $constraints, $groups);
        if ($errors->count() > 0) {
            throw new ValidationFailedException($value, $errors);
        }
    }
}
