<?php

namespace OrderBundle\Test\Service;

use PHPUnit\Framework\TestCase;
use OrderBundle\Entity\Customer;
use OrderBundle\Service\DiscountService;
use OrderBundle\Service\DiscountEleventhPurchase;
use OrderBundle\Service\DiscountTwentiethPurchase;
use OrderBundle\Service\DiscountFiftiethPurchase;
use OrderBundle\Service\DiscountHundredthPurchase;

class DiscountServiceTest extends TestCase
{
	private Customer $customer;
	private DiscountService $discountService;

	public function setUp(): void
	{
		$this->customer = $this->createStub(Customer::class);
		$this->discountService = new DiscountService();
		$this->discountService
			->addDiscount(new DiscountEleventhPurchase())
			->addDiscount(new DiscountTwentiethPurchase())
			->addDiscount(new DiscountFiftiethPurchase())
			->addDiscount(new DiscountHundredthPurchase());
	}

	/**
	 * @test
	 * @dataProvider dataProvider
	*/
	public function getDiscount($customerTotalOrders, $expectedResult)
	{
		$this->customer
			->method('getTotalOrders')
			->willReturn($customerTotalOrders);

		$discount = $this->discountService->getDiscount($this->customer);

		$this->assertEquals($expectedResult, $discount);
	}

	public function dataProvider()
	{
		return [
			'shouldGiveFivePercentDiscountWhenIsEleventhPurchase' => [
				'customerTotalOrders' => 11,
				'expectedResult' => 5.0,
			],
			'shouldGiveTenPercentDiscountWhenIsTwentiethPurchase' => [
				'customerTotalOrders' => 20,
				'expectedResult' => 10,
			],
			'shouldGiveFiftyPercentDiscountWhenIsFiftiethPurchase' => [
				'customerTotalOrders' => 50,
				'expectedResult' => 50,
			],
			'shouldGiveFullDiscountWhenIsHundredthPurchase' => [
				'customerTotalOrders' => 100,
				'expectedResult' => 100,
			],
		];
	}
}
