<?php

namespace App\Card;

use App\Card\Card;

class CardHand
{
    private $hand = [];

    public function add(Card $card): void
    {
        $this->hand[] = $card;
    }

    public function getCards(): array
    {
        $cards = [];
        foreach ($this->hand as $card) {
            $cards[] = [
                'suit' => $card->getSuit(),
                'value' => $card->getValue()
            ];
        }
        return $cards;
    }

}
