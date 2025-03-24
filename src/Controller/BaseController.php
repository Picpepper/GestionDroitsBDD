<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Doctrine\ORM\Exception\RepositoryException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class BaseController extends AbstractController
{
    #[Route('/', name: 'app_base')]
    public function index(UserRepository $userRepository): Response
    {
        $users = $userRepository->findAll();

        return $this->render('base.html.twig', [
            'users' => $users
        ]);
    }
}
