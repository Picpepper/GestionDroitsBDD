<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\GestionDonneesForm;
use App\Form\ModifUserForm;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Routing\Attribute\Route;

final class BaseController extends AbstractController
{
    #[Route('/', name: 'app_base')]
    public function index(): Response
    {
        return $this->render('base.html.twig', []);
    }

    #[Route('/liste-user', name: 'app_liste_user')]
    public function afficher_users(Request $request, UserRepository $userRepository): Response
    {
        $users = $userRepository->findAll();
        $form = $this->createForm(GestionDonneesForm::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->get('recharger')->isClicked()) {
                $command = ['php', 'bin/console', 'doctrine:fixtures:load', '--no-interaction'];
                $this->addFlash('notice', 'Vous avez généré un total de 10 utilisateurs exactement');
            } elseif ($form->get('ajouter')->isClicked()) {
                $command = ['php', 'bin/console', 'doctrine:fixtures:load', '--append', '--no-interaction'];
                $this->addFlash('notice', 'Vous avez ajouté 10 nouveaux utilisateurs à votre liste');
            }

            $process = new Process($command);
            $process->setWorkingDirectory($this->getParameter('kernel.project_dir'));

            try {
                $process->mustRun();
            } catch (ProcessFailedException $exception) {
                $this->addFlash('danger', 'Erreur lors de l’exécution de la commande : ' . $exception->getMessage());
            }

            return $this->redirectToRoute('app_liste_user');
        }

        return $this->render('base/liste-user.html.twig', [
            'users' => $users,
            'form' => $form->createView()
        ]);
    }

    // Code non fonctionnel. L'erreur "Warning: Array to string conversion" ne veut pas se résoudre malgré mes nombreuses tentatives quand je rejoins cette page, je vous laisse voir.
    #[Route('/modifier-user/{id}', name: 'app_modifier_user')]
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

                return $this->redirectToRoute('app_liste_user');
            }
        }

        return $this->render('action/modifier-user.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/supprimer-user/{id}', name: 'app_supprimer_user')]
    public function supprimer_user(User $user, EntityManagerInterface $eMI): Response
    {
        if ($user != null) {
            $eMI->remove($user);
            $eMI->flush();
            $this->addFlash('notice', 'Utilisateur supprimée');
        }
        return $this->redirectToRoute('app_liste_user');
    }
}