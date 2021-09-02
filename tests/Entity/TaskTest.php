<?php

namespace App\tests\Entity;

use App\Entity\Task;
use App\Entity\User;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Input\StringInput;

class TaskTest extends KernelTestCase {
    
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

    //Create a mock object
    public function getEntity() : Task {
        return (new Task())
            ->setCreatedAt(new \DateTime())
            ->setTitle("Test de titre")
            ->setContent("Test de contenu de la tâche")
            ->setIsDone(false)
            ->setUser(new User())
        ;
    }

    public function assertHasErrors(Task $task, int $number = 0)
    {
        self::bootKernel();
        // Call the validator to test the entity is respecting format
        $errors = static::getContainer()->get('validator')->validate($task);

        // Get all the error messages if present
        $messages = [];

        /** @var ConstraintViolation $error */

        foreach($errors as $error)
        {
            $messages[] = $error->getPropertyPath() . ' : ' . $error->getMessage();
        }
        
        $this->assertCount($number, $errors, implode(', ', $messages));
    }

    public function testInstanceOfEntity()
    {
        $this->assertInstanceOf(Task::class, $this->getEntity());
    }


    // Check object has all expected attributes
    public function testObjectHasAllAttributes()
    {
        $this->assertObjectHasAttribute('id', new Task);
        $this->assertObjectHasAttribute('createdAt', new Task);
        $this->assertObjectHasAttribute('title', new Task);
        $this->assertObjectHasAttribute('content', new Task);
        $this->assertObjectHasAttribute('isDone', new Task);
    }
    
    public function testValidEntity()
    {
        $this->assertHasErrors($this->getEntity(), 0);
    }


    // Check constraints with not blank
    public function testTitleNotBlank()
    {
        $task = $this->getEntity()->setTitle('');
        $this->assertHasErrors($task, 1);
    }

    public function testContentNotBlank()
    {
        $task = $this->getEntity()->setContent('');
        $this->assertHasErrors($task, 1);
    }

    public function testCreatedAt()
    {
        $dateTask = $this->getEntity()->getCreatedAt();
        $this->assertInstanceOf(DateTime::class, $dateTask);
        
    }

    public function testIsDone()
    {
        $isDoneStatus = $this->getEntity()->getIsDone();
        $this->assertIsBool($isDoneStatus);
    }

    protected function tearDown(): void
    {
        $this->runCommand('doctrine:database:drop --force');

        parent::tearDown();

    }

}