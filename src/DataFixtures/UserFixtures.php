<?php

namespace App\DataFixtures;

use App\Entity\Task;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Faker\Factory;

/**
 * @codeCoverageIgnore
 */
class UserFixtures extends Fixture
{

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr_FR');

        // Create one testUser - visitor role
        $user = new User();
        $user->setEmail('testUser@mail.com')
             ->setPassword($this->hasher->hashPassword(
                $user,
                'testUser'))
             ->setRoles(['ROLE_USER'])
             ->setUsername('testUser');
        
        for($j = 0; $j < 2; $j++)
        {
            $task = new Task();
            $task->setTitle($faker->sentence())
                 ->setCreatedAt($faker->dateTimeBetween('-2 months'))
                 ->setContent($faker->paragraph(1))
                 ->setIsDone($faker->boolean())
                 ->setUser($user);
 
            $manager->persist($task);
        }

        $manager->persist($user);

        // Create one test User - visitor role
        $user = new User();
        $user->setEmail('testAdmin@mail.com')
             ->setPassword($this->hasher->hashPassword($user,'testAdmin'))
             ->setRoles(['ROLE_ADMIN'])
             ->setUsername('testAdmin');

        for($j = 0; $j < 2; $j++)
        {
            $task = new Task();
            $task->setTitle($faker->sentence())
                 ->setCreatedAt($faker->dateTimeBetween('-2 months'))
                 ->setContent($faker->paragraph(1))
                 ->setIsDone($faker->boolean())
                 ->setUser($user);
      
            $manager->persist($task);
        }

        $manager->persist($user);

        // Create the "anonymous" user
                $user = new User();
                $user->setEmail('anonymous@mail.com')
                     ->setPassword($this->hasher->hashPassword($user,'anonymous'))
                     ->setRoles(['ROLE_USER'])
                     ->setUsername('Anonymous');
        
                $manager->persist($user);

        $manager->flush();
    }
}
