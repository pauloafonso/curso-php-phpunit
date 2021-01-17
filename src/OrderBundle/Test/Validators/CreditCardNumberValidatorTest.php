<?php
namespace OrderBundle\Test\Validators;

use PHPUnit\Framework\TestCase;
use OrderBundle\Validators\CreditCardNumberValidator;

class CreditCardNumberValidatorTest extends TestCase
{
    /**
	 * @test
	 */
    public function shouldNotBeValidWhenValueIsNotNumeric()
    {
        $creditCardNumberValidator = new CreditCardNumberValidator("foo");

        $this->assertFalse($creditCardNumberValidator->isValid());
    }

    /**
	 * @test
	 */
    public function shouldNotBeValidWhenValueIsNotCreditCardNumber()
    {
        $creditCardNumberValidator = new CreditCardNumberValidator(123);

        $this->assertFalse($creditCardNumberValidator->isValid());
    }

    /**
	 * @test
	 */
    public function shouldBeValidWhenValueIsCreditCardNumber()
    {
        $creditCardNumberValidator = new CreditCardNumberValidator(1234567891234567);

        $this->assertTrue($creditCardNumberValidator->isValid());
    }

    /**
	 * @test
	 */
    public function shouldBeValidWhenValueIsCreditCardNumberAsString()
    {
        $creditCardNumberValidator = new CreditCardNumberValidator('1234567891234567');

        $this->assertTrue($creditCardNumberValidator->isValid());
    }

    /**
	 * @test
	 */
    public function shouldNotBeValidWhenValueIsEmpty()
    {
        $creditCardNumberValidator = new CreditCardNumberValidator("");

        $this->assertFalse($creditCardNumberValidator->isValid());
    }
}
