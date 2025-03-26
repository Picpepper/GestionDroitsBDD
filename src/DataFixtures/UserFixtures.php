<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    private $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $lastUser = $manager->getRepository(User::class)
            ->createQueryBuilder('user')
            ->where('user.email LIKE :pattern')
            ->setParameter('pattern', 'test%@test.fr')
            ->orderBy('user.id', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        $lastNumber = 0;
        if ($lastUser) {
            preg_match('/test(\d+)@test.fr/', $lastUser->getEmail(), $matches);
            if (isset($matches[1])) {
                $lastNumber = (int)$matches[1];
            }
        }

        $startNumber = $lastNumber + 1;

        echo "Dernier utilisateur trouvé : test{$lastNumber}@test.fr\n";
        echo "Création des utilisateurs à partir de : test{$startNumber}@test.fr\n";

        for ($i = 0; $i < 10; $i++) {
            $number = $startNumber + $i;
            $email = "test{$number}@test.fr";

            $user = new User();
            $user->setEmail($email);
            $user->setPassword($this->passwordHasher->hashPassword($user, 'test5'));
            $user->setRoles(['ROLE_USER']);

            $manager->persist($user);
        }
        $manager->flush();
    }
}
