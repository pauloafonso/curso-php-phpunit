<?php

namespace OrderBundle\Service;

use OrderBundle\Entity\Customer;

class DiscountHundredthPurchase implements DiscountInterface
{
	public function isEligible(Customer $customer): bool
	{
		if ($customer->getTotalOrders() == 100) {
			return true;
		}
		return false;
	}

	public function getAmount(): float
	{
		return 100.0;
	}
}
