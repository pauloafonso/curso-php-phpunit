<?php
namespace OrderBundle\Test\Validators;

use PHPUnit\Framework\TestCase;
use OrderBundle\Validators\NumericValidator;

class NumericValidatorTest extends TestCase
{
    /**
	 * @test
	 * @dataProvider valueProvider
	 */
    public function isValid($value, $expectedResult)
    {
        $numericValidator = new NumericValidator($value);

        $this->assertEquals($expectedResult, $numericValidator->isValid());
    }

    public function valueProvider()
    {
        return [
            'shouldBeValidWhenValueIsNumeric' => ['value' => 123, 'expectedResult' => true],
            'shouldBeValidWhenValueIsNumericString' => ['value' => "123", 'expectedResult' => true],
            'shouldNotBeValidWhenValueIsNotNumeric' => ['value' => "abc", 'expectedResult' => false],
            'shouldNotBeValidWhenValueIsEmpty' => ['value' => "", 'expectedResult' => false],
        ];
    }
}
