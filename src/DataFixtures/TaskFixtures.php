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
class TaskFixtures extends Fixture
{
    private $hasher;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr_FR');
        // Create  3 Users - visitor role and assign them 2 tasks
        for($i = 0; $i < 3; $i++)
        {
            $user = new User();
            $user->setEmail($faker->email())
                 ->setPassword($this->hasher->hashPassword(
                    $user,
                    $faker->password()))
                 ->setRoles(['ROLE_USER'])
                 ->setUsername($faker->username());
            
            $manager->persist($user);

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
        }

        // Create older non-assigned tasks
        for($k = 0; $k < 5; $k++)
        {
            $task = new Task();
            $task->setTitle($faker->sentence())
                ->setCreatedAt($faker->dateTimeBetween('-4 months', '-2 months'))
                ->setContent($faker->paragraph(1))
                ->setIsDone($faker->boolean());

            $manager->persist($task);
        }

        $manager->flush();
    }
}
