<?php

namespace App\Service;

use InvalidArgumentException;

class MathService implements MathServiceInterface
{
    public function divide(float $dividend, float $divisor): float
    {
        if (0.0 === $divisor) {
            throw new InvalidArgumentException('Divisor cannot be 0.');
        }

        return $dividend / $divisor;
    }
}
