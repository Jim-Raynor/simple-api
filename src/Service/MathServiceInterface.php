<?php

namespace App\Service;

use InvalidArgumentException;

interface MathServiceInterface
{
    /**
     * @param float $dividend
     * @param float $divisor
     *
     * @return float
     *
     * @throws InvalidArgumentException
     */
    public function divide(float $dividend, float $divisor): float;
}
