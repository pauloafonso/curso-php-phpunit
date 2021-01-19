<?php

namespace OrderBundle\Service;

use OrderBundle\Entity\Customer;

interface DiscountInterface
{
	public function isEligible(Customer $customer): bool;
	public function getAmount(): float;
}
