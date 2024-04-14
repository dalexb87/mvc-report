<?php

namespace App\Card;

class CardGraphic extends Card
{
    private $suitGraphic = [
        'hearts' => '♥️',
        'diamonds' => '♦️',
        'clubs' => '♣️',
        'spades' => '♠️',
    ];

    private $valueGraphic = [
        'ace' => '🂡',
        '2' => '🂢',
        '3' => '🂣',
        '4' => '🂤',
        '5' => '🂥',
        '6' => '🂦',
        '7' => '🂧',
        '8' => '🂨',
        '9' => '🂩',
        '10' => '🂪',
        'jack' => '🂫',
        'queen' => '🂭',
        'king' => '🂮',
    ];

    private $suit;
    private $value;

    public function __construct($suit, $value)
    {
        parent::__construct($suit, $value);

        $this->suit = $suit;
        $this->value = $value;
    }

    public function getSuit(): string
    {
        return $this->suitGraphic[$this->suit];
    }

    public function getValue(): string
    {
        return $this->valueGraphic[$this->value];
    }

    public function getAsString(): string
    {
        return $this->valueGraphic[$this->getValue() - 1] . $this->suitGraphic[$this->getSuit()];
    }
}
