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
	*/
	public function shouldGiveFivePercentDiscountWhenIsEleventhPurchase()
	{
		$this->customer
			->method('getTotalOrders')
			->willReturn(11);

		$discount = $this->discountService->getDiscount($this->customer);

		$this->assertEquals(5.0, $discount);
	}

	/**
	 * @test
	*/
	public function shouldGiveTenPercentDiscountWhenIsTwentiethPurchase()
	{
		$this->customer
			->method('getTotalOrders')
			->willReturn(20);

		$discount = $this->discountService->getDiscount($this->customer);

		$this->assertEquals(10.0, $discount);
	}

	/**
	 * @test
	*/
	public function shouldGiveFiftyPercentDiscountWhenIsFiftiethPurchase()
	{
		$this->customer
			->method('getTotalOrders')
			->willReturn(50);

		$discount = $this->discountService->getDiscount($this->customer);

		$this->assertEquals(50.0, $discount);
	}

	/**
	 * @test
	*/
	public function shouldGiveFullDiscountWhenIsHundredthPurchase()
	{
		$this->customer
			->method('getTotalOrders')
			->willReturn(100);

		$discount = $this->discountService->getDiscount($this->customer);

		$this->assertEquals(100.0, $discount);
	}
}
/*
Discount rules

The 11 purchase -> 5%
The 20 purchase -> 10%
The 50 purchase -> 50%
The 100 purchase -> 100%
*/