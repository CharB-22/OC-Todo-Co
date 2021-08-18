<?php

use App\Entity\Task;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class TaskTest extends KernelTestCase {
    

    //Create a mock object
    public function getEntity(): Task
    {
        return (new Task())
        ->setTitle('Tâche à faire')
        ->setContent('C\'est la description de la tâche.')
        ->setCreatedAt(new \DateTime())
        ->setUser(new User())
        ->setIsDone(true);
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
    
    public function testGetCreatedAt()
    {
        $this->assertInstanceOf(DateTime::class, $this->getEntity()->getCreatedAt());
    }

    public function testIsDone()
    {
        $this->assertIsBool($this->getEntity()->isDone());
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

    public function testCreatedAtNotBlank()
    {
        $task = $this->getEntity()->setIsDone('');
        $this->assertHasErrors($task, 1);
    }

    public function testToggle()
    {
        $task = $this->getEntity();
        $task->toggle(!$task->isDone());

        return $this->assertNotTrue($task->isDone());
    }
}