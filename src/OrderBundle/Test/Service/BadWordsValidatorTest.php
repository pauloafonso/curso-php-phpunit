<?php

namespace OrderBundle\Test\Service;

use PHPUnit\Framework\TestCase;
use OrderBundle\Service\BadWordsValidator;
use OrderBundle\Repository\BadWordsRepository;

class BadWordsValidatorTest extends TestCase
{
    /**
     * @test
     * @dataProvider badWordsDataProvider
     */
    public function hasBadWords($badWordsList, $value, $expectedResult)
    {
        $badWordsRepository = $this->createMock(BadWordsRepository::class);
        $badWordsRepository
            ->method("findAllAsArray")
            ->willReturn($badWordsList);

        $badWordsValidator = new BadWordsValidator($badWordsRepository);

        $this->assertEquals($expectedResult, $badWordsValidator->hasBadWords($value));
    }

    public function badWordsDataProvider()
    {
        return [
            'shouldHasBadWordsWhenValueHasABadWord' => [
                'badWordsList' => ['shit', 'fuck', 'bitch'],
                'value' => "I don't give a fuck",
                'expectedResult' => true,
            ],
            'shouldNotHasBadWordsWhenValueHasNotABadWord' => [
                'badWordsList' => ['shit', 'fuck', 'bitch'],
                'value' => "Thank you very much!",
                'expectedResult' => false,
            ],
            'shouldNotHasBadWordsWhenValueIsEmpty' => [
                'badWordsList' => ['shit', 'fuck', 'bitch'],
                'value' => "",
                'expectedResult' => false,
            ],
            'shouldNotHasBadWordsWhenBadWordsListIsEmpty' => [
                'badWordsList' => [],
                'value' => "I don't give a fuck",
                'expectedResult' => false,
            ],
        ];
    }
}