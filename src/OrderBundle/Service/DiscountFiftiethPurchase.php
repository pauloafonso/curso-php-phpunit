<?php

namespace OrderBundle\Service;

use OrderBundle\Entity\Customer;

class DiscountFiftiethPurchase implements DiscountInterface
{
	public function isEligible(Customer $customer): bool
	{
		if ($customer->getTotalOrders() == 50) {
			return true;
		}
		return false;
	}

	public function getAmount(): float
	{
		return 50.0;
	}
}
