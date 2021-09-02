<?php

use App\Repository\TaskRepository;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Input\StringInput;

class TaskRepositoryTest extends KernelTestCase {
    
    public function runCommand($string) : int {

        $kernel = static::createKernel();
        $application = new Application($kernel);
        $application->setAutoExit(false);
        
        return $application->run(new StringInput(sprintf('%s --quiet', $string)));
    }

    public function setUp(): void
    {
        // Initialize the database for each tests (test environment)
        $this->runCommand('doctrine:database:drop --force --env=test');
        $this->runCommand('doctrine:database:create --env=test');
        $this->runCommand('doctrine:schema:create --env=test');
        $this->runCommand('doctrine:fixtures:load --env=test');

        $this->runCommand('app:link-anonymous');        
    }

    //Make sure the database has some fixtures to test
    public function testCount()
    {
        self::bootKernel();
        $tasks = static::getContainer()->get(TaskRepository::class)->count([]);

        $this->assertEquals(15, $tasks);
    }

    protected function tearDown(): void
    {
        $this->runCommand('doctrine:database:drop --force');

        parent::tearDown();

    }
}