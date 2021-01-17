<?php
namespace PaymentBundle\Test\Service; 

use PHPUnit\Framework\TestCase;
use PaymentBundle\Service\Gateway;
use MyFramework\HttpClientInterface;
use MyFramework\LoggerInterface;

class GatewayTest extends TestCase
{
	/**
	 * @test
	 * @dataProvider paymentDataProvider
	*/
	public function payment(
		$user,
		$password,
		$name,
		$creditCardNumber,
		$validity,
		$value,
		$expectedResult,
	) {
		$httpClient = $this->createStub(HttpClientInterface::class);

		$httpClient
			->method('send')
			->will($this->returnCallback(function ($httpMethod, $uri, $body) {
				return $this->fakeHttpClient($httpMethod, $uri, $body);
			}));

		$logger = $this->createMock(LoggerInterface::class);

		$gateway = new Gateway(
			$httpClient,
			$logger,
			$user,
			$password
		);

		$paid = $gateway->pay(
			$name,
			$creditCardNumber,
			$validity,
			$value,
		);

		$this->assertEquals($expectedResult, $paid);
	}

	private function fakeHttpClient($httpMethod, $uri, $body)
	{
		switch ($uri) {
			case Gateway::BASE_URL . '/authenticate':
				if ($body['password'] == 'valid-password') {
					return 'some-token';
				}
				return null;
				break;
			case Gateway::BASE_URL . '/pay':
				if ($body['creditCardNumber'] == '0000000000000000') {
					return ['paid' => false];
				}
				return ['paid' => true];
				break;
			default:
				# code...
				break;
		}
	}

	public function paymentDataProvider()
	{
		return [
			'shouldNotPayWhenAuthenticationFails' => [
				'user' => 'paulo_lima',
				'password' => 'invalid-password',
				'name' => 'paulo lima gatti',
				'creditCardNumber' => '1234567891234567',
				'validity' => new \DateTime('2022-12-20'),
				'value' => 434.43,
				'expectedResult' => false,
			],
			'shouldNotPayWhenPaymentFails' => [
				'user' => 'paulo_lima',
				'password' => 'valid-password',
				'name' => 'paulo lima gatti',
				'creditCardNumber' => '0000000000000000',
				'validity' => new \DateTime('2022-12-20'),
				'value' => 434.43,
				'expectedResult' => false,
			],
			'shouldPayIfHasTokenAndPaymentConfirmation' => [
				'user' => 'paulo_lima',
				'password' => 'valid-password',
				'name' => 'paulo lima gatti',
				'creditCardNumber' => '1234567891234567',
				'validity' => new \DateTime('2022-12-20'),
				'value' => 434.43,
				'expectedResult' => true,
			],
		];
	}
}
