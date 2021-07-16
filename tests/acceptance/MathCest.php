<?php

namespace App\Tests;

class MathCest
{
    public function correctDividingTest(AcceptanceTester $I)
    {
        $I->sendGet('/divide/100/20');
        $I->canSeeResponseContainsJson([
            'success' => true,
            'result' => 5,
        ]);
    }

    public function zeroDivisorTest(AcceptanceTester $I)
    {
        $I->sendGet('/divide/100/0');
        $I->canSeeResponseContainsJson([
            'success' => false,
            'code' => 0,
            'error' => 'validation error',
            'data' => [
                'violations' => [
                    [
                        'propertyPath' => 'divisor',
                        'type' => 'urn:uuid:aa2e33da-25c8-4d76-8c6c-812f02ea89dd',
                    ],
                ]
            ]
        ]);
    }
}
