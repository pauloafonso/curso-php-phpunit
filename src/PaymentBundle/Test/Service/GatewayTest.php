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
	 * @dataProvider paymentUsingFakeApproachDataProvider
	*/
	public function payUsingFakeApproach(
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

	/**
	 * @test
	*/
	public function shouldPayIfIsAuthenticatedAndPayPostIsSuccessfulUsingOnConsecutiveCalls()
	{
		$httpClient = $this->createMock(HttpClientInterface::class);
		$httpClient
			->expects($this->atLeast(2))
			->method('send')
			->will($this->onConsecutiveCalls('token', ['paid' => true]));

		$logger = $this->createMock(LoggerInterface::class);
		$logger
			->expects($this->never())
			->method('log');

		$user = "paulo_lima";
		$password = "valid-password";

		$gateway = new Gateway(
			$httpClient,
			$logger,
			$user,
			$password,
		);

		$gateway->pay('paulo lima gatti', '1234567891234567', new \DateTime('now'), 50.00);
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

	public function paymentUsingFakeApproachDataProvider()
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

	/**
	 * @test
	*/
	public function shouldNotPayWhenAuthenticationFailsUsingMock()
	{
		$user = 'paulo_lima';
		$password = 'invalid-password';
		$name = 'paulo lima gatti';
		$creditCardNumber = '1234567891234567';
		$validity = new \DateTime('2022-12-20');
		$value = 434.43;

		$httpClient = $this->createMock(HttpClientInterface::class);

		$map = [
			'POST',
			Gateway::BASE_URL . '/authenticate',
			[
	            'user' => $user,
	            'password' => $password,
	        ],
	        null,
		];

		$httpClient
			->expects($this->once())
			->method('send')
			->will($this->returnValueMap($map));

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

		$expectedResult = false;

		$this->assertEquals($expectedResult, $paid);
	}

	/**
	 * @test
	*/
	public function shouldNotPayWhenPaymentFailsUsingMock()
	{
		$user = 'paulo_lima';
		$password = 'invalid-password';
		$name = 'paulo lima gatti';
		$creditCardNumber = '1234567891234567';
		$validity = new \DateTime('2022-12-20');
		$value = 434.43;

		$httpClient = $this->createMock(HttpClientInterface::class);

		$map = [
			[
				'POST',
				Gateway::BASE_URL . '/authenticate',
				[
		            'user' => $user,
		            'password' => $password,
		        ],
		        'my-token',
			],
			[
				'POST',
				Gateway::BASE_URL . '/pay',
				[
		            'name' => $name,
		            'creditCardNumber' => $creditCardNumber,
		            'validity' => $validity,
		            'value' => $value,
		            'token' => 'my-token',
				],
				['paid' => false],
	        ],
		];

		$httpClient
			->expects($this->atLeast(2))
			->method('send')
			->will($this->returnValueMap($map));

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

		$expectedResult = false;

		$this->assertEquals($expectedResult, $paid);
	}

	/**
	 * @test
	 * @dataProvider mockReturnValueMapDataProvider
	*/
	public function paymentMockUsingDataProvider(
		$user,
		$password,
		$name,
		$creditCardNumber,
		$validity,
		$value,
		$expects,
		$map,
		$expectedResult,
	) {
		$httpClient = $this->createMock(HttpClientInterface::class);

		$httpClient
			->expects($expects)
			->method('send')
			->will($this->returnValueMap($map));

		$logger = $this->createMock(LoggerInterface::class);

		$gateway = new Gateway(
			$httpClient,
			$logger,
			$user,
			$password,
		);

		$paid = $gateway->pay(
			$name,
			$creditCardNumber,
			$validity,
			$value,
		);

		$this->assertEquals($expectedResult, $paid);
	}

	public function mockReturnValueMapDataProvider()
	{
		$user = 'paulo_lima';
		$password = 'password';
		$name = 'paulo lima gatti';
		$creditCardNumber = '1234567891234567';
		$validity = new \DateTime('2022-12-20');
		$value = 434.43;

		return [
			'shouldNotPayWhenAuthenticationFailsUsingMock' => [
				'user' => $user,
				'password' => $password,
				'name' => $name,
				'creditCardNumber' => $creditCardNumber,
				'validity' => $validity,
				'value' => $value,
				// this once function indicates the expected behaviour - the method has to be called only once
				'expects' => $this->once(),
				'map' => [
					// in the map array, I have to set the arguments values, in the real order, and then the return value
					'POST',
					Gateway::BASE_URL . '/authenticate',
					[
			            'user' => $user,
			            'password' => $password,
			        ],
			        // defining the return value as null, making this authentication fails
			        null,
				],
				// so we expect false as result
				'expectedResult' => false,
			],
			'shouldNotPayWhenPaymentFailsUsingMock' => [
				'user' => $user,
				'password' => $password,
				'name' => $name,
				'creditCardNumber' => $creditCardNumber,
				'validity' => $validity,
				'value' => $value,
				// this once function indicates the expected behaviour - the method has to be called at least twice - because of the first called successful
				'expects' => $this->atLeast(2),
				'map' => [
					[
						'POST',
						Gateway::BASE_URL . '/authenticate',
						[
				            'user' => $user,
				            'password' => $password,
				        ],
			        	// defining the return value as 'my-token', making this authentication successful
				        'my-token',
					],
					[
						'POST',
						Gateway::BASE_URL . '/pay',
						[
				            'name' => $name,
				            'creditCardNumber' => $creditCardNumber,
				            'validity' => $validity,
				            'value' => $value,
				            'token' => 'my-token',
						],
						// defining the return value as false, making this post fails
						['paid' => false],
			        ],
				],
				'expectedResult' => false,
			],
			'shouldPayIfHasTokenAndPaymentConfirmationUsingMock' => [
				'user' => $user,
				'password' => $password,
				'name' => $name,
				'creditCardNumber' => $creditCardNumber,
				'validity' => $validity,
				'value' => $value,
				// it will pass through this method twice
				'expects' => $this->atLeast(2),
				'map' => [
					[
						'POST',
						Gateway::BASE_URL . '/authenticate',
						[
				            'user' => $user,
				            'password' => $password,
				        ],
				        // returns the token as is excpected
				        'my-token',
					],
					[
						'POST',
						Gateway::BASE_URL . '/pay',
						[
				            'name' => $name,
				            'creditCardNumber' => $creditCardNumber,
				            'validity' => $validity,
				            'value' => $value,
				            'token' => 'my-token',
						],
						// defining the return value as true, making this post successful
						['paid' => true],
			        ],
				],
				// so in this case the expected result is true!
				'expectedResult' => true,
			],
		];
	}
}
