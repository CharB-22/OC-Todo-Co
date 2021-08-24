<?php

namespace App\Tests\Controllers;

use App\Entity\User;
use App\Tests\NeedLogin;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class UserControllerTest extends WebTestCase 
{

    use NeedLogin;

    public function getEntity() {
        return static::getContainer()->get('doctrine')->getManager()->getRepository(User::class)->findOneBy(['username' => 'testAdmin']);
    }

    public function testUserListDisplay()
    {
        $client = static::createClient();
        $client->request('GET', '/users');
        // The visitor is redirected to the login page
        $this->assertResponseRedirects('/login');
    }

    public function testCreateUserDisplayRestricted()
    {
        $client = static::createClient();
        $client->request('GET', '/users/create');
        // The visitor is redirected to the login page 
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
        $this->assertResponseRedirects('/login');
    }

}