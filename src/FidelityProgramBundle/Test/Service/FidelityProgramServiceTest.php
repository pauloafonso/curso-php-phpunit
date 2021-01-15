<?php

namespace FidelityProgramBundle\Service;

use PHPUnit\Framework\TestCase;
use FidelityProgramBundle\Service\FidelityProgramService;
use FidelityProgramBundle\Repository\PointsRepository;
use FidelityProgramBundle\Service\PointsCalculator;
use OrderBundle\Entity\Customer;

class FidelityProgramServiceTest extends TestCase
{
	/**
	* @test
	*/
	public function shouldSavePointsWhenRecivePoints()
	{
		$pointsCalculator = $this->createMock(PointsCalculator::class);
		$pointsCalculator
			->method('calculatePointsToReceive')
			->willReturn(400);

		$pointsRepository = $this->createMock(PointsRepository::class);
		$pointsRepository
			->expects($this->once())
			->method('save');

		$customer = $this->createMock(Customer::class);

		$fidelityProgramService = new FidelityProgramService($pointsRepository, $pointsCalculator);
		$fidelityProgramService->addPoints($customer, 50);
	}

	/**
	 * @test
	*/
	public function shouldNotSavePointsWhenThereAreNotPointsToRecive()
	{
		$pointsRepository = $this->createMock(PointsRepository::class);
		$pointsRepository
			->expects($this->never())
			->method('save');

		$pointsCalculator = $this->createMock(PointsCalculator::class);
		$pointsCalculator
			->method('calculatePointsToReceive')
			->willReturn(0);

		$customer = $this->createMock(Customer::class);

		$fidelityProgramService = new FidelityProgramService($pointsRepository, $pointsCalculator);
		$fidelityProgramService->addPoints($customer, 50);
	}
}