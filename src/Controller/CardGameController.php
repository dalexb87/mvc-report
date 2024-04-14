<?php

namespace App\Controller;

use App\Card\Card;
use App\Card\CardGraphic;
use App\Card\CardHand;
use App\Card\DeckOfCards;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CardGameController extends AbstractController
{
    #[Route("/card", name: "card")]
    public function cardgame(): Response
    {

        return $this->render('card/index.html.twig');
    }

    #[Route("/card/deck", name: "view_deck")]
    public function deck(SessionInterface $session): Response
    {
        // Hämta kortlek från session
        $deck = $session->get('deck', []);

        // Skapa en instans av DeckOfCards, stoppa in sessionens kortlek
        $deckOfCards = new DeckOfCards($deck);

        // Hämta kortleken
        $deck = $deckOfCards->getCards();

        // Spara kortleken i session
        $session->set('deck', $deck);

        return $this->render('card/deck.html.twig', [
            'deck' => $deck,
        ]);
    }

    #[Route("/card/deck/shuffle", name: "shuffle_deck")]
    public function shuffleDeck(SessionInterface $session): Response
    {
        // Hämta kortlek från session
        //$deck = $session->get('deck', []);

        $deckOfCards = new DeckOfCards();
        $deckOfCards->shuffle();

        // Hämta den blandade kortleken
        $shuffledDeck = $deckOfCards->getCards();

        // Uppdatera kortleken i sessionen med den blandade kortleken
        $session->set('deck', $shuffledDeck);

        return $this->render('card/deck.html.twig', [
            'deck' => $shuffledDeck
        ]);
    }

    #[Route("/card/deck/draw", name: "draw_next")]
    public function draw(SessionInterface $session): Response
    {

        $deck = $session->get('deck', []);

        $deckOfCards = new DeckOfCards($deck);

        // Dra ett kort från kortleken
        $draw = $deckOfCards->dealCard();

        // Ser till så draget kort är borttaget ur array
        $session->set('deck', $deckOfCards->getCards());


        $remainingCards = $deckOfCards->getRemainingCardsCount();

        return $this->render('card/draw.html.twig', [
            'drawn' => $draw,
            'remainingCards' => $remainingCards,
        ]);
    }

    #[Route("/card/deck/draw/{num<\d+>}", name: "draw_cards")]
    public function drawCards(int $num, SessionInterface $session): Response
    {
        $deck = $session->get('deck', []);

        $drawnCards = [];
        $deckOfCards = new DeckOfCards($deck);

        // tar antal från inparameter och loopar igenom array för att dra lika många kort med dealCard()
        for ($i = 0; $i < $num; $i++) {

            $drawnCards[] = $deckOfCards->dealCard();
        }

        // Uppdaterar array i session med aktuell kortlek
        $session->set('deck', $deckOfCards->getCards());

        $remainingCards = $deckOfCards->getRemainingCardsCount();

        // Rendera en vy och skicka med dragna kort och antalet kvarvarande kort
        return $this->render('card/draw_cards.html.twig', [
            'drawnCards' => $drawnCards,
            'remainingCards' => $remainingCards,
        ]);
    }
}
