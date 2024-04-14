<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class SessionController extends AbstractController
{
    #[Route("/session", name: "session")]
    public function index(SessionInterface $session): Response
    {

        $sessionData = $session->all();

        return $this->render('session.html.twig', [
            'sessionData' => $sessionData
        ]);
    }

    #[Route("/session/delete", name: "session_delete")]
    public function delete(SessionInterface $session): Response
    {
        // Rensar sessionen
        $session->clear();

        $this->addFlash(
            'success',
            'Nu är sessionen raderad.'
        );

        return $this->redirectToRoute('session');
    }
}
