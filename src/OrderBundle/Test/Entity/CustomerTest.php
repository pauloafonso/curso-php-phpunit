<?php
namespace OrderBundle\Test\Entity;

use PHPUnit\Framework\TestCase;
use OrderBundle\Entity\Customer;

class CustomerTest extends TestCase
{
    /**
     * @test
     * @dataProvider valuesProvider
     */
    public function isAllowedToOrder($isActive, $isBlocked, $expectedResult)
    {
        $customer = new Customer(
            $isActive,
            $isBlocked,
            "Paulo Lima",
            "+5531999888777"
        );

        $this->assertEquals($expectedResult, $customer->isAllowedToOrder());
    }

    public function valuesProvider()
    {
        return [
            'shouldBeAllowedWhenIsActiveAndIsNotBlocked' => [
                'isActive' => true,
                'isBlocked' => false,
                'expectedResult' => true,
            ],
            'shouldNotBeAllowedWhenIsNotActiveAndIsBlocked' => [
                'isActive' => false,
                'isBlocked' => true,
                'expectedResult' => false,
            ],
            'shouldNotBeAllowedWhenIsNotActiveAndIsNotBlocked' => [
                'isActive' => false,
                'isBlocked' => false,
                'expectedResult' => false,
            ],
            'shouldNotBeAllowedWhenIsActiveAndIsBlocked' => [
                'isActive' => true,
                'isBlocked' => true,
                'expectedResult' => false,
            ],
        ];
    }
}
