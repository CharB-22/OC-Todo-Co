<?php

namespace App\Tests\Controllers;
    
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class SecurityControllerTest extends WebTestCase 
{

    public function testDisplayLoginPage()
    {
        $client = static::createClient();
        $client->request('GET', '/login');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertSelectorTextContains('h1', 'Se connecter');
        $this->assertSelectorNotExists('.alert.alert-danger'); 
    }

    public function testLoginWithValidCredentials()
    {
        $client = static::createClient();
        $csrf_token = $client->getContainer()->get('security.csrf.token_manager')->getToken('authenticate');
        $client->request('POST', '/login', [
            '_csrf_token' => $csrf_token,
            'email' => 'testUser@mail.com',
            'password' => 'testUser'
        ]);

        $this->assertResponseRedirects('/tasks');

    }

    public function testLoginWithInvalidCredentials()
    {
        $client = static::createClient();
        $csrf_token = $client->getContainer()->get('security.csrf.token_manager')->getToken('authenticate');
        $client->request('GET', '/login', [
            '_csrf_token' => $csrf_token,
            'email' => 'user@mail.com',
            'password' => 'fakePassword'
        ]);

        $this->assertResponseRedirects('/login');

        $client->followRedirect();
        $this->assertSelectorExists('.alert.alert-danger');
    }

    public function testLogout()
    {
        $client = static::createClient();
        $client->request('GET', '/logout');
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
    
    }
}