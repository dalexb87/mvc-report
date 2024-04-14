<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MyController extends AbstractController
{
    #[Route("/", name: "home")]
    public function home(): Response
    {
        return $this->render('home.html.twig');
    }

    #[Route("/about", name: "about")]
    public function about(): Response
    {
        return $this->render('about.html.twig');
    }

    #[Route("/report", name: "report")]
    public function report(): Response
    {
        return $this->render('report.html.twig');
    }

    #[Route("/lucky", name: "lucky")]
    public function rando(): Response
    {

        $number = random_int(0, 100);

        $images = [
            'img/H.jpg',
            'img/T.jpg',
            'img/S.jpg'
        ];

        $image = $images[array_rand($images)];


        $elements = '';

        for ($i = 0; $i < 5; $i++) {

            $red = rand(0, 255);
            $green = rand(0, 255);
            $blue = rand(0, 255);

            $color = "rgb($red, $green, $blue)";

            $elements .= '<div class="random-element" style="top:' . rand(0, 500) . 'px; left:' . rand(0, 1500) . 'px; background-color:' . $color . ';"></div>';
        }


        $data = [
            'number' => $number,
            'image' => $image,
            'balls' => $elements
        ];

        return $this->render('lucky_number.html.twig', $data);
    }
}
