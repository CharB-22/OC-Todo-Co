<?php

use App\Repository\TaskRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class TaskRepositoryTest extends KernelTestCase {
    

    //Make sure the database has some fixtures to test
    public function testCount()
    {
        self::bootKernel();
        $tasks = static::getContainer()->get(TaskRepository::class)->count([]);

        $this->assertEquals(11, $tasks);
    }
}