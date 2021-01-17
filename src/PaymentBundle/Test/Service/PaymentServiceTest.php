<?php
namespace PaymentBundle\Test\Service; 

use PHPUnit\Framework\TestCase;
use PaymentBundle\Service\PaymentService;
use MyFramework\HttpClientInterface;
use MyFramework\LoggerInterface;
use OrderBundle\Entity;
use PaymentBundle\Service\Gateway;
use PaymentBundle\Repository\PaymentTransactionRepository;

class PaymentServiceTest extends TestCase
{
	/**
	 * @test
	*/
	public function shouldSavePaymentTransactionWhenPaymentIsMadeAfterRetries()
	{
		$gateway = $this->createMock(Gateway::class);

		$gateway
			->expects($this->atLeast(3))
			->method('pay')
			->will($this->onConsecutiveCalls(false, false, true));

		$paymentTransactionRepository = $this->createMock(PaymentTransactionRepository::class);

		$paymentTransactionRepository
			->expects($this->once())
			->method('save');

		$paymentService = new PaymentService($gateway, $paymentTransactionRepository);
		
		$customer = $this->createMock(Entity\Customer::class);
		$item = $this->createMock(Entity\Item::class);
		$creditCard = $this->createMock(Entity\CreditCard::class);

		$paymentService->pay($customer, $item, $creditCard);
	}
}
