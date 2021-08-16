<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        // Create one testUser - visitor role
        $user = new User();
        $user->setEmail('testUser@mail.com')
             ->setPassword($this->encoder->encodePassword(
                $user,
                'testUser'))
             ->setRoles(['ROLE_USER'])
             ->setUsername('testUser');
             

        $manager->persist($user);

        // Create one test User - visitor role
        $user = new User();
        $user->setEmail('testAdmin@mail.com')
             ->setPassword($this->encoder->encodePassword($user,'testAdmin'))
             ->setRoles(['ROLE_ADMIN'])
             ->setUsername('testAdmin');

        $manager->persist($user);

        $manager->flush();
    }
}
