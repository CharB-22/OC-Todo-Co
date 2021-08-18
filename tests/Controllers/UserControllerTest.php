<?php

namespace App\Tests\Controllers;

use App\Entity\User;
use App\Tests\NeedLogin;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class UserControllerTest extends WebTestCase 
{

    use NeedLogin;

    // Check admin pages are restricted
    public function testUserListRestricted()
    {
        $client = static::createClient();
        $client->request('GET', '/users');
        // The visitor is redirected to the login page
        $this->assertResponseRedirects('/login', Response::HTTP_FOUND);
    }

    public function testCreateUserRestricted()
    {
        $client = static::createClient();
        $client->request('GET', '/users/create');
        // The visitor is redirected to the login page 
        $this->assertResponseRedirects('/login', Response::HTTP_FOUND);
    }

    public function testAdminUserList()
    {
        $client = static::createClient();

        // Pick the admin user in the database
        self::bootKernel();
        $user = static::getContainer()->get('doctrine')->getManager()->getRepository(User::class)->findOneBy(['username' => 'testAdmin']);
        
        $this->login($client, $user);

        $client->request('GET', '/users');

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }
}