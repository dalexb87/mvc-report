<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

use App\Card\DeckOfCards;

class ApiController extends AbstractController
{
    private $router;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    #[Route("/api", name: "api")]
    public function index(): Response
    {
        // Tar fram alla routes
        $routes = $this->router->getRouteCollection()->all();

        // Används för att filtrera visade routes
        $prefix = '/_';

        $jsonRoutes = [];

        // Söker igenom alla routes, om prefix i route upptäcks skippa dessa
        foreach ($routes as $route) {
            if (strpos($route->getPath(), $prefix) !== 0) {
                $jsonRoutes[] = [
                    'url' => $route->getPath()
                ];
            }
        }

        $myRoutes = [];

        $routes =
            [
                '/',
                '/about',
                '/report',
                '/lucky',
                '/api',
                '/api/quote',
                '/card',
                'api/deck',
                'api/deck/shuffle',
                'api/deck/draw',
                'api/deck/draw/3'
            ];

        $descs =
            [
                'Landningssida "Om mig"',
                'Om kursen länk till github',
                'Redovisningssida',
                'Sida som tar fram ett "magiskt nummer" och en slumpmässig bild m.m.',
                'visar alla routes (denna sidan)',
                'Ger ett dagens citat i JSON-format',
                'Landningssida för kortspelet',
                'Skriver ut en kortlek sorterad efter färg och värde i JSON-format',
                'POST-request för att blanda om kortleken, returnerar sedan resultat JSON-format',
                'POST-request drar ett kort samt visar antal kort kvar i JSON-format',
                'POST-request drar flera (3) kort och visar antal kort kvar i JSON-format'
            ];


        foreach ($routes as $index => $route) {
            $myRoutes[] = ['route' => $route, 'description' => $descs[$index]];
        }

        return $this->render('api.html.twig', [
            'jsonRoutes' => $jsonRoutes,
            'routes' => $myRoutes
        ]);
    }

    #[Route("/api/quote", name: "api_quote")]
    public function quote(): JsonResponse
    {
        // Array med citat
        $quotes = [
            "The luxury of hope was given to me by the Terminator. Because if a machine can learn the value of human life, maybe we can too. - Sarah Connor",
            "The Only Constant in Life Is Change. - Heraclitus",
            "All I know is that I know nothing. - Socrates"
        ];

        // Ta fram  slumpmässigt citat från array
        $todaysQuote = $quotes[array_rand($quotes)];

        return $this->json([
            'quote' => $todaysQuote,
            'date' => date('Y-m-d'),
            'timestamp' => time()
        ]);
    }

    #[Route("/api/deck", name: "api_deck", methods: ['GET'])]
    public function deck(): JsonResponse
    {

        // Ny instans av klass DeckOfCards
        $deckOfCards = new DeckOfCards();

        // Kallar metod getCards() som hämtar array med kort
        $deckOfCards = $deckOfCards->getCards();

        // Loopar igenom kortleken och lagrar respektive suit/value för enskilt kort i ny array (för att konvertera till json)
        foreach ($deckOfCards as $card) {
            $deckCards[] = [
                'suit' => $card->getSuit(),
                'value' => $card->getValue(),
            ];
        }

        // Returnerar array i json-format
        return $this->json([
            'deck' => $deckCards,
        ], 200, [], ['json_encode_options' => JSON_UNESCAPED_UNICODE]);
    }

    #[Route("/api/deck/shuffle", name: "api_deck_shuffle", methods: ['POST'])]
    public function shuffle(SessionInterface $session): JsonResponse
    {
        // Hämta kortlek från session
        //$deck = $session->get('deck', []);

        $deckOfCards = new DeckOfCards();
        $deckOfCards->shuffle();

        // Hämta den blandade kortleken
        $shuffledDeck = $deckOfCards->getCards();

        // Uppdatera kortleken i sessionen med den blandade kortleken
        $session->set('deck', $shuffledDeck);

        foreach ($shuffledDeck as $card) {
            $shuffledDeckData[] = [
                'suit' => $card->getSuit(),
                'value' => $card->getValue(),
            ];
        }

        // Returnera den blandade kortleken som JSON
        return $this->json([
            'deck' => $shuffledDeckData,
        ], 200, [], ['json_encode_options' => JSON_UNESCAPED_UNICODE]);
    }

    #[Route("/api/deck/draw", name: "api_draw_next", methods: ['POST'])]
    public function draw(SessionInterface $session): JsonResponse
    {
        $deck = $session->get('deck', []);

        $deckOfCards = new DeckOfCards($deck);

        // Dra ett kort från kortleken
        $draw = $deckOfCards->dealCard();

        // Ser till så draget kort är borttaget ur array
        $session->set('deck', $deckOfCards->getCards());


        $remainingCards = $deckOfCards->getRemainingCardsCount();

        return $this->json([
        'drawn' => [
            'suit' => $draw->getSuit(),
            'value' => $draw->getValue(),
        ],
            'remainingCards' => $remainingCards,
        ], 200, [], ['json_encode_options' => JSON_UNESCAPED_UNICODE]);
    }

    #[Route("/api/deck/draw/{num<\d+>}", name: "api_draw_cards", methods: ['POST'])]
    public function drawCards(int $num, SessionInterface $session): JsonResponse
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

        // Loopar igenom arrayen med alla dragna kort och lagrar respektive suit/value för enskilt kort i ny array
        foreach ($drawnCards as $drawnCard) {
            $drawnCardsData[] = [
                'suit' => $drawnCard->getSuit(),
                'value' => $drawnCard->getValue(),
            ];
        }

        // Returnera dragna kort och kvarstående som JSON
        return $this->json([
            'drawn' => $drawnCardsData,
            'remainingCards' => $remainingCards,
        ], 200, [], ['json_encode_options' => JSON_UNESCAPED_UNICODE]);
    }
}
