<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Services\PaginatorService;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    #[Route('/admin', name: 'admin')]
    public function index(Request $request, UserRepository $userRepository, PaginatorInterface $paginator, PaginatorService $paginatorService): Response
    {
        $donnees = $userRepository->findAll();

        $users = $paginatorService->paginator($request, $donnees, $paginator);

        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
            'users' =>  $users,
        ]);
    }
}
