<?php

namespace PaymentBundle\Entity;

use OrderBundle\Entity\Customer;
use OrderBundle\Entity\Item;

class PaymentTransaction
{
    public function __construct(
    	private Customer $customer,
    	private Item $item,
    	private $value,
    ) { }
}