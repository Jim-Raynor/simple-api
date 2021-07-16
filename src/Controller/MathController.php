<?php

namespace App\Controller;

use App\DTO\DivideRequestData;
use App\Service\MathServiceInterface;
use App\Validator\ValidatorAwareInterface;
use App\Validator\ValidatorTrait;

class MathController implements ValidatorAwareInterface
{
    use ValidatorTrait;

    private MathServiceInterface $mathService;

    public function __construct(MathServiceInterface $mathService)
    {
        $this->mathService = $mathService;
    }

    public function divide($dividend, $divisor): float
    {
        $requestData = new DivideRequestData();
        $requestData->dividend = $dividend;
        $requestData->divisor = $divisor;

        $this->validate($requestData);

        return $this->mathService->divide((float) $requestData->dividend, (float) $requestData->divisor);
    }
}
