<?php

namespace App\Tests\Controllers;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\HttpFoundation\Response;

class SecurityControllerTest extends WebTestCase 
{
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
        $client->request('POST', '/login', [
            '_csrf_token' => $csrf_token,
            'email' => 'user@mail.com',
            'password' => 'fakePassword'
        ]);

        $this->assertResponseRedirects('/login');

        $client->followRedirect();
        $this->assertSelectorExists('.alert.alert-danger');
    }

    protected function tearDown(): void
    {
        $this->runCommand('doctrine:database:drop --force');

        parent::tearDown();

    }
}