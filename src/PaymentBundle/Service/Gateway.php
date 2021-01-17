<?php

namespace PaymentBundle\Service;

use MyFramework\HttpClientInterface;
use MyFramework\LoggerInterface;

class Gateway
{
    const BASE_URL = 'https://paymentgateway.ex';

    public function __construct(
        private HttpClientInterface $httpClient,
        private LoggerInterface $logger,
        private string $user,
        private string $password,
    )
    { }

    public function pay($name, $creditCardNumber, \DateTime $validity = null, $value): bool
    {
        $token = $this->httpClient->send('POST', self::BASE_URL . '/authenticate', [
            'user' => $this->user,
            'password' => $this->password,
        ]);

        if (!$token) {
            $this->logger->log('Authentication failed');
            return false;
        }

        $response = $this->httpClient->send('POST', self::BASE_URL . '/pay', [
            'name' => $name,
            'creditCardNumber' => $creditCardNumber,
            'validity' => $validity,
            'value' => $value,
            'token' => $token,
        ]);

        if (!$response['paid'] === true) {
            $this->logger->log('Payment failed');
            return false;
        }

        return true;
    }
}