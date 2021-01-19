<?php

namespace OrderBundle\Service;

use OrderBundle\Entity\Customer;

class DiscountEleventhPurchase implements DiscountInterface
{
	public function isEligible(Customer $customer): bool
	{
		if ($customer->getTotalOrders() == 11) {
			return true;
		}
		return false;
	}

	public function getAmount(): float
	{
		return 5.0;
	}
}
