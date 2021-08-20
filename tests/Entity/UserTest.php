<?php

use App\Entity\Task;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserTest extends KernelTestCase {

    public function assertHasErrors(User $user, int $number = 0)
    {
        self::bootKernel();
        // Call the validator to test the entity is respecting format
        $errors = static::getContainer()->get('validator')->validate($user);
        
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
        $this->assertInstanceOf(User::class, $this->getEntity());
    }

    public function testGetId()
    {
        // Pick a user in the database
        self::bootKernel();
        $user = static::getContainer()->get('doctrine')->getManager()->getRepository(User::class)->findOneBy(['username' => 'testUser']);
        
        $this->assertEquals(11, $user->getId());
    }

    public function testGetTask()
    {
        // Pick a user in the database
        self::bootKernel();
        $user = static::getContainer()->get('doctrine')->getManager()->getRepository(User::class)->findOneBy(['id' => 8 ]);

        // Make sure this existing user has 2 tasks registered to his name
        $this->assertEquals(2, count($user->getTasks()));
    } 

    //Make sure the database has some fixtures to test
    public function getEntity(): User
    {
        $user = new User();
        $user->setEmail('caseUser@mail.com')
             ->setPassword(password_hash('userCase', PASSWORD_DEFAULT))
             ->setRoles(['ROLE_USER'])
             ->setUsername('caseUser');
   
        return $user;
    }
   

    public function testGetEmail()
    {
        $this->assertEquals('caseUser@mail.com', $this->getEntity()->getEmail());
    }

    public function testAddTask()
    {
        $user = $this->getEntity();
        
        $task = new Task();

        $task->setTitle('Tâche à faire')
             ->setContent('C\'est la description de la tâche.')
             ->setCreatedAt(new \DateTime())
             ->setUser($user)
             ->setIsDone(true);
        
        $user->addTask($task);

        $this->assertEquals(1, count($user->getTasks()));
    }

    public function testRemoveTask()
    {
        // Pick a user in the database
        self::bootKernel();
        $user = static::getContainer()->get('doctrine')->getManager()->getRepository(User::class)->findOneBy(['id' => 8 ]);

        // TBC
    }

    // Check object has all expected attributes
    public function testObjectHasAllAttributes()
    {
        $this->assertObjectHasAttribute('id', new User);
        $this->assertObjectHasAttribute('email', new User);
        $this->assertObjectHasAttribute('roles', new User);
        $this->assertObjectHasAttribute('password', new User);
        $this->assertObjectHasAttribute('username', new User);
    }

    public function testValidEntity()
    {
        $this->assertHasErrors($this->getEntity(), 0);
    }

    public function testEmailNotBlank()
    {
        $task = $this->getEntity()->setEmail('');
        $this->assertHasErrors($task, 1);
    }

    public function testUsernameNotBlank()
    {
        $task = $this->getEntity()->setUsername('');
        $this->assertHasErrors($task, 1);
    }

    public function testPasswordNotBlank()
    {
        $task = $this->getEntity()->setPassword('');
        $this->assertHasErrors($task, 1);
    }


    // Test unicity of key email
    public function testEmailNotUnique()
    {
        $task = $this->getEntity()->setEmail('testUser@mail.com');
        $this->assertHasErrors($task, 1);
    }


}