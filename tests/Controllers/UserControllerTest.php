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
        $admin = static::getContainer()->get('doctrine')->getManager()->getRepository(User::class)->findOneBy(['username' => 'testAdmin']);
        return $admin;
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

    public function testUserListDisplayAuthorized()
    {
        $client = static::createClient();

        $user = $this->getEntity();
        $this->login($client, $user);
        
        $client->request('GET', '/users');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertSelectorTextContains('h1', 'Liste des utilisateurs');
    }

    public function testCreateUserFormUnauthorized()
    {
        $client = static::createClient();
        $client->request('GET', '/users/create');
        // The visitor is redirected to the login page
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
    }

    public function testCreateUser()
    {
        $client = static::createClient();

        $user = $this->getEntity();
        $this->login($client, $user);

        $crawler = $client->request('GET', '/users/create');
        
        $form = $crawler->selectButton('Ajouter')->form();
        $form['user[username]'] = 'usernameTest';
        $form['user[password][first]'] = 'passwordTest';
        $form['user[password][second]'] = 'passwordTest';
        $form['user[email]'] = 'emailTest';
        $form['user[roles]'] = ['ROLE_ADMIN'];
        $client->submit($form);

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function testEditUserFormUnauthorized()
    {
        $client = static::createClient();
        $client->request('GET', '/users/8/edit');
        // The visitor is redirected to the login page
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
    }

    public function testEditUserAuthorized()
    {
        $client = static::createClient();

        $client->request('GET', '/users/8/edit');
        // The visitor is redirected to the login page
        $user = $this->getEntity();
        $this->login($client, $user);
        
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
    }

}