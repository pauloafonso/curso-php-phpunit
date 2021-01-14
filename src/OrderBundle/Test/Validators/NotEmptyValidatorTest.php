<?php
namespace OrderBundle\Test\Validators;

use PHPUnit\Framework\TestCase;
use OrderBundle\Validators\NotEmptyValidator;

class NotEmptyValidatorTest extends TestCase
{
	public function testShouldBeValidWhenValueIsNotEmpty()
	{
		$notEmptyValidator = new NotEmptyValidator("foo");

		$this->assertTrue($notEmptyValidator->isValid());
	}

	/**
	 * @test
	*/
	public function shouldNotBeValidWhenValueIsEmpty()
	{
		$notEmptyValidator = new NotEmptyValidator("");

		$this->assertFalse($notEmptyValidator->isValid());
	}

	/**
	 * @test
	 * @dataProvider shouldBeValidWhenValueIsNotEmptyValueProvider
	*/
	public function shouldBeValidWhenValueIsNotEmptyWithDataProvider($value)
	{
		$notEmptyValidator = new NotEmptyValidator($value);
		$this->assertTrue($notEmptyValidator->isValid());
	}

	public function shouldBeValidWhenValueIsNotEmptyValueProvider()
	{
		return [
			'shouldBeValidWhenValueIsNotEmpty' => [
				'value' => "foo",
			]
		];
	}

	/**
	 * @test
	 * @dataProvider valueProvider
	 */
	public function isValid($value, $expectedResult)
	{
		$notEmptyValidator = new NotEmptyValidator($value);

		$this->assertEquals($expectedResult, $notEmptyValidator->isValid());
	}

	public function valueProvider()
	{
		return [
			'shouldBeValidWhenValueIsNotEmpty' => [
				'value' => 'foo',
				'expectedResult' => true,
			],
			'shouldNotBeValidWhenValueIsEmpty' => [
				'value' => '',
				'expectedResult' => false,
			],
		];
	}
}
