<?php

namespace App\Validator;

use Symfony\Component\Validator\Validator\ValidatorInterface;

interface ValidatorAwareInterface
{
    public function setValidator(ValidatorInterface $validator): void;
}
