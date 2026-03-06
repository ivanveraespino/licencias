<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(private UserPasswordHasherInterface $userPassworHasher)
    {
        
    }
    public function load(ObjectManager $manager): void
    {
        $user = new User();
        $user->setUsername('46301964');
        $userPassworHasher=$this->userPassworHasher->hashPassword($user, '123456');
        $user->setPassword($userPassworHasher);
        $manager->persist($user);

        $manager->flush();
    }
}
