<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ModifUserForm;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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

    // Code non fonctionnel. L'erreur "Warning: Array to string conversion" ne veut pas se résoudre malgré mes nombreuses tentatives quand je rejoins cette page, je vous laisse voir.
    #[Route('/modifier/{id}', name: 'app_modifier_user')]
    public function modifier_user(Request $request, User $user, EntityManagerInterface $eMI): Response
    {
        $form = $this->createForm(ModifUserForm::class, $user);
        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $roles = $form->get('roles')->getData();
                $user->setRoles($roles);

                $eMI->persist($user);
                $eMI->flush();

                return $this->redirectToRoute('app_base');
            }
        }

        return $this->render('action/modifier-user.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
