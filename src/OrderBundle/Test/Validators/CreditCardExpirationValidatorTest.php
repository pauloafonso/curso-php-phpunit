<?php
namespace OrderBundle\Test\Validators;

use PHPUnit\Framework\TestCase;
use OrderBundle\Validators\CreditCardExpirationValidator;

class CreditCardExpirationValidatorTest extends TestCase
{
    /**
	 * @test
	 * @dataProvider valueProvider
	 */
    public function isValid($value, $expectedResult)
    {
        $creditCardExpirationValidator = new CreditCardExpirationValidator($value);

        $this->assertEquals($expectedResult, $creditCardExpirationValidator->isValid());
    }

    public function valueProvider()
    {
        $dateAfterNow = new \DateTime();
        $dateAfterNow->add(new \DateInterval('P1D'));

        $dateBeforeNow = new \DateTime();
        $dateBeforeNow->sub(new \DateInterval('P1D'));

        return [
            'shouldBeValidWhenValueIsDateAfterNow' => ['value' => $dateAfterNow, 'expectedResult' => true],
            'shouldNotBeValidWhenValueIsDateBeforeToday' => ['value' => $dateBeforeNow, 'expectedResult' => false],
        ];
    }
}
