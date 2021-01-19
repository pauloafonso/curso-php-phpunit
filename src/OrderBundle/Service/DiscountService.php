<?php

namespace OrderBundle\Service;

use OrderBundle\Entity\Customer;

class DiscountService
{
	private array $discounts;

	public function addDiscount(DiscountInterface $discount)
	{
		$this->discounts[] = $discount;
		return $this;
	}

	public function getDiscount(Customer $customer): float
	{
		foreach ($this->discounts as $discount) {
			if ($discount->isEligible($customer)) {
				return $discount->getAmount();
			}
		}
		return 0.0;
	}
}
