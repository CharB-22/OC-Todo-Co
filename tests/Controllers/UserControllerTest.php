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

    public function testDisplayLogin()
    {
        $client = static::createClient();
        $client->request('GET', '/login');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertSelectorTextContains('h1', 'Se connecter');
    }

    public function testUserListDisplayUnauthorized()
    {
        $client = static::createClient();
        $client->request('GET', '/users');
        // The visitor is redirected to the login page
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
    }

    public function testCreateUserFormUnauthorized()
    {
        $client = static::createClient();
        $client->request('GET', '/users/create');
        // The visitor is redirected to the login page
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
    }

    /*
    public function testCreateUser()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/users/create');
        $form = $crawler->selectButton('Ajouter')->form();
        $form['user[username]'] = 'usernameTest';
        $form['user[password][first]'] = 'passwordTest';
        $form['user[password][second]'] = 'passwordTest';
        $form['user[email]'] = 'emailTest';
        $form['user[roles]'] = 'ROLE_USER';
        $client->submit($form);

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        /*$this->assertResponseRedirects('/users');
        $client->followRedirect();
        $this->assertSelectorExists('.alert.alert-success');
    }*/

}