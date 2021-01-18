<?php

namespace OrderBundle\Test\Service;

use PHPUnit\Framework\TestCase;
use OrderBundle\Service\OrderService;
use PaymentBundle\Service\PaymentService;
use OrderBundle\Repository\OrderRepository;
use OrderBundle\Service\BadWordsValidator;
use FidelityProgramBundle\Service\FidelityProgramService;
use OrderBundle\Entity\Customer;
use OrderBundle\Entity\Item;
use OrderBundle\Entity\CreditCard;
use OrderBundle\Exception;
use PaymentBundle\Entity\PaymentTransaction;

class OderServiceTest extends TestCase
{
	private BadWordsValidator $badWordsValidator;
	private PaymentService $paymentService;
	private OrderRepository $orderRepository;
	private FidelityProgramService $fidelityProgramService;
	private Customer $customer;
    private Item $item;
    private string $description;
    private CreditCard $creditCard;

	public function setUp(): void
	{
		$this->badWordsValidator = $this->createMock(BadWordsValidator::class);
		$this->paymentService = $this->createMock(PaymentService::class);
		$this->orderRepository = $this->createMock(OrderRepository::class);
		$this->fidelityProgramService = $this->createMock(FidelityProgramService::class);
		$this->customer = $this->createMock(Customer::class);
        $this->item = $this->createMock(Item::class);
        $this->description = 'order description';
        $this->creditCard = $this->createMock(CreditCard::class);
	}

	/**
	* @test
	*/
	public function shouldNotProcessWhenCustomerIsNotAllowed()
	{
		$this
			->withCustomerNotAllowed()
			->withItemAvailable()
			->withBadWordsNotFound();

		$this->expectException(Exception\CustomerNotAllowedException::class);

		$orderService = new OrderService(
			$this->badWordsValidator,
			$this->paymentService,
			$this->orderRepository,
			$this->fidelityProgramService,
		);

		$orderService->process(
	        $this->customer,
	        $this->item,
	        $this->description,
	        $this->creditCard,
		);
	}

	/**
	* @test
	*/
	public function shouldNotProcessWhenItemIsNotAvaliable()
	{
		$this
			->withCustomerAllowed()
			->withItemNotAvailable()
			->withBadWordsNotFound();

		$this->expectException(Exception\ItemNotAvailableException::class);

		$orderService = new OrderService(
			$this->badWordsValidator,
			$this->paymentService,
			$this->orderRepository,
			$this->fidelityProgramService,
		);

		$orderService->process(
	        $this->customer,
	        $this->item,
	        $this->description,
	        $this->creditCard,
		);
	}

	/**
	* @test
	*/
	public function shouldNotProcessWhenHasBadWords()
	{
		$this
			->withCustomerAllowed()
			->withItemAvailable()
			->withBadWordsFound();

		$this->expectException(Exception\BadWordsFoundException::class);

		$orderService = new OrderService(
			$this->badWordsValidator,
			$this->paymentService,
			$this->orderRepository,
			$this->fidelityProgramService,
		);

		$orderService->process(
	        $this->customer,
	        $this->item,
	        $this->description,
	        $this->creditCard,
		);
	}

	/**
	* @test
	*/
	public function shouldPayAndSaveOrder()
	{
		$this
			->withCustomerAllowed()
			->withItemAvailable()
			->withBadWordsNotFound();

		$this->orderRepository
			->expects($this->once())
			->method('save');

		$paymentTransaction = $this->createMock(PaymentTransaction::class);

		$this->paymentService
			->method('pay')
			->willReturn($paymentTransaction);

		$orderService = new OrderService(
			$this->badWordsValidator,
			$this->paymentService,
			$this->orderRepository,
			$this->fidelityProgramService,
		);

		$order = $orderService->process(
	        $this->customer,
	        $this->item,
	        $this->description,
	        $this->creditCard,
		);

		$this->assertNotEmpty($order->getPaymentTransaction());
	}

	private function withCustomerNotAllowed()
	{
		$this->customer
			->method('isAllowedToOrder')
			->willReturn(false);

		return $this;
	}

	private function withCustomerAllowed()
	{
		$this->customer
			->method('isAllowedToOrder')
			->willReturn(true);

		return $this;
	}

	private function withItemAvailable()
	{
		$this->item
			->method('isAvailable')
			->willReturn(true);

		return $this;
	}

	private function withItemNotAvailable()
	{
		$this->item
			->method('isAvailable')
			->willReturn(false);

		return $this;
	}

	private function withBadWordsFound()
	{
		$this->badWordsValidator
			->method('hasBadWords')
			->willReturn(true);

		return $this;
	}

	private function withBadWordsNotFound()
	{
		$this->badWordsValidator
			->method('hasBadWords')
			->willReturn(false);

		return $this;
	}
}
