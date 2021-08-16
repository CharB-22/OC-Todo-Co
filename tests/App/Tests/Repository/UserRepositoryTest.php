<?php

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class UserRepositoryTest extends KernelTestCase {
    

    //Make sure the database has some fixtures to test
    public function testCount()
    {
        self::bootKernel();
        $users = static::getContainer()->get(UserRepository::class)->count([]);

        $this->assertEquals(5, $users);
    }
}