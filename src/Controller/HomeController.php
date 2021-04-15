<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/home', name: 'home')]
    public function index(): Response
    {
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController'
            
        ]);
    }

    #[Route('/mentions-legales', name: 'mentions')]
    public function mentions(): Response
    {
        return $this->render('home/mentions.html.twig');
    }
}
