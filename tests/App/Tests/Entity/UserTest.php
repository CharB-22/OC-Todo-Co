<?php

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserTest extends KernelTestCase {
    

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

    public function testEmailNotUnique()
    {
        $task = $this->getEntity()->setEmail('testUser@mail.com');
        $this->assertHasErrors($task, 1);
    }


}