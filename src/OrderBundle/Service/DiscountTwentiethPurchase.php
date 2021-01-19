<?php

namespace OrderBundle\Service;

use OrderBundle\Entity\Customer;

class DiscountTwentiethPurchase implements DiscountInterface
{
	public function isEligible(Customer $customer): bool
	{
		if ($customer->getTotalOrders() == 20) {
			return true;
		}
		return false;
	}

	public function getAmount(): float
	{
		return 10.0;
	}
}
