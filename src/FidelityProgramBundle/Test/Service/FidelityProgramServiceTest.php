<?php

namespace FidelityProgramBundle\Service;

use PHPUnit\Framework\TestCase;
use FidelityProgramBundle\Service\FidelityProgramService;
use FidelityProgramBundle\Repository\PointsRepositoryInterface;
use FidelityProgramBundle\Service\PointsCalculator;
use MyFramework\LoggerInterface;
use OrderBundle\Entity\Customer;


class FidelityProgramServiceTest extends TestCase
{
	
	public function shouldSavePointsWhenRecivePoints()
	{
		$pointsCalculator = $this->createStub(PointsCalculator::class);
		$pointsCalculator
			->method('calculatePointsToReceive')
			->willReturn(400);

		$pointsRepository = $this->createMock(PointsRepositoryInterface::class);
		$pointsRepository
			->expects($this->once())
			->method('save');

		$logger = $this->createMock(LoggerInterface::class);
		$logger->method('log');

		$customer = $this->createMock(Customer::class);

		$fidelityProgramService = new FidelityProgramService($pointsRepository, $pointsCalculator, $logger);
		$fidelityProgramService->addPoints($customer, 50);
	}

	
	public function shouldNotSavePointsWhenThereAreNotPointsToRecive()
	{
		$pointsRepository = $this->createMock(PointsRepositoryInterface::class);
		$pointsRepository
			->expects($this->never())
			->method('save');

		$pointsCalculator = $this->createStub(PointsCalculator::class);
		$pointsCalculator
			->method('calculatePointsToReceive')
			->willReturn(0);

		$logger = $this->createMock(LoggerInterface::class);
		$logger->method('log');

		$customer = $this->createMock(Customer::class);

		$fidelityProgramService = new FidelityProgramService($pointsRepository, $pointsCalculator, $logger);
		$fidelityProgramService->addPoints($customer, 50);
	}

	/**
  	 * @test
	*/
	public function shouldWriteLogOfPointsAddedWhenPointsAreAdded()
	{
		// $pointsRepository->save
		// pointsCalculator->calculatePointsToReceive
		// logger->log

		$pointsRepository = $this->createMock(PointsRepositoryInterface::class);
		$pointsRepository->method('save');

		$pointsCalculator = $this->createStub(PointsCalculator::class);
		$pointsCalculator->method('calculatePointsToReceive')->willReturn(50);

		$logMessages = [];
		$logger = $this->createMock(LoggerInterface::class);
		$logger
			->method('log')
			->will($this->returnCallback(function ($logMessage) use (&$logMessages) {
				$logMessages[] = $logMessage;
			}));

		$fidelityProgramService = new FidelityProgramService(
			$pointsRepository,
			$pointsCalculator,
			$logger
		);

		$customer = $this->createMock(Customer::class);

		$fidelityProgramService->addPoints($customer, 50);

		$expectedLogMessages = [
			'entering the addPoints function',
			'points has just been added',
		];
		$this->assertEquals($expectedLogMessages, $logMessages);
	}
}
