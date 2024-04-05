<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

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

        return $this->render('api.html.twig', [
            'jsonRoutes' => $jsonRoutes,
        ]);
    }
    
    #[Route("/api/quote", name: "api_quote")]
    public function quote(): JsonResponse
    {
        // Array med citat
        $quotes = [
            "Citat 1",
            "Citat 2",
            "Citat 3"
        ];
        
        // Ta fram  slumpmässigt citat från array
        $todaysQuote = $quotes[array_rand($quotes)];
        
        return $this->json([
            'quote' => $todaysQuote,
            'date' => date('Y-m-d'),
            'timestamp' => time()
        ]);
    }
}
