<?php
namespace PaymentBundle\Test\Service; 

use PHPUnit\Framework\TestCase;
use PaymentBundle\Service\PaymentService;
use MyFramework\HttpClientInterface;
use MyFramework\LoggerInterface;
use OrderBundle\Entity;
use PaymentBundle\Service\Gateway;
use PaymentBundle\Exception\PaymentErrorException;
use PaymentBundle\Repository\PaymentTransactionRepository;

class PaymentServiceTest extends TestCase
{
	private $gateway;
	private $paymentTransactionRepository;
	private $paymentService;
	private $customer;
	private $item;
	private $creditCard;

	public function setUp(): void
	{
		$this->gateway = $this->createMock(Gateway::class);

		$this->paymentTransactionRepository = $this->createMock(PaymentTransactionRepository::class);

		$this->paymentService = new PaymentService($this->gateway, $this->paymentTransactionRepository);

		$this->customer = $this->createMock(Entity\Customer::class);
		$this->item = $this->createMock(Entity\Item::class);
		$this->creditCard = $this->createMock(Entity\CreditCard::class);
	}

	/**
	 * @test
	*/
	public function shouldSavePaymentTransactionWhenPaymentIsMadeAfterTwoRetries()
	{
		$this->gateway
			->expects($this->atLeast(3))
			->method('pay')
			->will($this->onConsecutiveCalls(false, false, true));

		$this->paymentTransactionRepository
			->expects($this->once())
			->method('save');

		$this->paymentService->pay($this->customer, $this->item, $this->creditCard);
	}

	/**
	 * @test
	*/
	public function shouldThrowExceptionAndNotSaveWhenGatewayFailsThreeTimes()
	{
		$this->gateway
			->expects($this->atLeast(3))
			->method('pay')
			->will($this->onConsecutiveCalls(false, false, false));

		$this->paymentTransactionRepository
			->expects($this->never())
			->method('save');

		$this->expectException(PaymentErrorException::class);

		$this->paymentService->pay($this->customer, $this->item, $this->creditCard);
	}

	public function tearDown(): void
	{
		unset($this->gateway);
		unset($this->paymentTransactionRepository);
		unset($this->paymentService);
		unset($this->customer);
		unset($this->item);
		unset($this->creditCard);
	}
}
