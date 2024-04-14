<?php

namespace App\Card;

class DeckOfCards
{
    private $cards;

    // Konstruktorn kan ta emot en array -> befintlig kortlek
    public function __construct(array $cards = [])
    {

        // Om ingen medskickad parameter finns / arrayen är tom -> bygg om kortleken
        if (empty($cards)) {
            $this->deck(); // bygger en ny kortlek
        } else {
            $this->cards = $cards; // Använd befintlig kortlek
        }
    }

    // Metod för att bygga en ny kortlek
    public function deck()
    {

        $suits = ['hearts', 'diamonds', 'clubs', 'spades'];
        $values = ['ace', '2', '3', '4', '5', '6', '7', '8', '9', '10', 'jack', 'queen', 'king'];

        foreach ($suits as $suit) {
            foreach ($values as $value) {
                $this->cards[] = new CardGraphic($suit, $value);
            }
        }
    }

    // Blanda kortleken
    public function shuffle()
    {
        shuffle($this->cards);
    }

    // Dra ett kort (sist i arrayen)
    public function dealCard()
    {
        return array_pop($this->cards);
    }

    // Hämtar antalet kort kvar
    public function getRemainingCardsCount()
    {
        return count($this->cards);
    }

    // Hämtar alla kort i kortleken
    public function getCards(): array
    {
        return $this->cards;
    }
}
