<?php

namespace FidelityProgramBundle\Service;

use FidelityProgramBundle\Entity\Points;
use FidelityProgramBundle\Repository\PointsRepositoryInterface;
use OrderBundle\Entity\Customer;
use MyFramework\LoggerInterface;

class FidelityProgramService
{
    public function __construct(
        private PointsRepositoryInterface $pointsRepository,
        private PointsCalculator $pointsCalculator,
        private LoggerInterface $logger,
    )
    {}

    public function addPoints(Customer $customer, $value)
    {
        $this->logger->log('entering the addPoints function');
        $pointsToAdd = $this->pointsCalculator->calculatePointsToReceive($value);

        if ($pointsToAdd > 0) {
            $points = new Points($customer, $pointsToAdd);
            $this->pointsRepository->save($points);
            $this->logger->log('points has just been added');
        }
    }
}