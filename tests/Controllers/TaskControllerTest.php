<?php

namespace App\Tests\Controllers;

use App\Entity\Task;
use App\Entity\User;
use App\Tests\NeedLogin;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;

class TaskControllerTest extends WebTestCase 
{

    use NeedLogin;
    
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

    public function getEntity($username) {
        $admin = static::getContainer()->get('doctrine')->getManager()->getRepository(User::class)->findOneBy(['username' => $username]);
        return $admin;
    }

    // Test to check all routes - GET methods
    public function testTaskList()
    {
        $client = static::createClient();
        $client->request('GET', '/tasks');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        // Add what it should display
        $this->assertSelectorExists('img', '.slide-image');
    }

    public function testDisplayAllTasks()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/tasks');
        
        $this->assertGreaterThan(
            0,
            $crawler->filter('.thumbnail')->count()
        );
    }

    public function testDisplayCreateTaskForm()
    {
        $client = static::createClient();
        $client->request('GET', '/tasks/create');

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertSelectorTextContains('h1', 'Créer une nouvelle tâche');
    }

    public function testCreateTask()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/tasks/create');
        $form = $crawler->selectButton('Ajouter')->form();
        $form['task[title]'] = 'Tâche nouvellement créer';
        $form['task[content]'] = 'Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.';
        $client->submit($form);
        
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $this->assertResponseRedirects('/tasks');
        $client->followRedirect();
        $this->assertSelectorExists('.alert.alert-success');

    }

    // Add test case where trying to access Create without login

    public function testDisplayEditTaskForm()
    {
        $client = static::createClient();
        $client->request('GET', '/tasks/1/edit');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK); 
        // Add what it should display
        $this->assertSelectorTextContains('h1', 'Modifier cette tâche');
    }

    public function testEditTask()
    {
            $client = static::createClient();
    
            $crawler = $client->request('GET', '/tasks/1/edit');
            $form = $crawler->selectButton('Modifier')->form();
            $form['task[title]'] = 'Tâche nouvellement créer';
            $client->submit($form);
            
            $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
            $this->assertResponseRedirects('/tasks');
            $client->followRedirect();
            $this->assertSelectorExists('.alert.alert-success');
    }

    public function testAdminDeleteTask()
    {
        $client = static::createClient();

        $user = $this->getEntity('testAdmin');
        $this->login($client, $user);

        $client->request('GET', '/tasks/2/delete');

        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $client->followRedirect();
        $this->assertSelectorExists('.alert.alert-success');
    }

    public function testAnonymousDeleteTask()
    {
        $client = static::createClient();

        $user = $this->getEntity('Anonymous');
        $this->login($client, $user);

        $client->request('GET', '/tasks/2/delete');

        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $client->followRedirect();
        $this->assertSelectorExists('.alert.alert-danger');
    }


    public function testToggleTask()
    {
        $client = static::createClient();

        $client->request('GET', '/tasks/4/toggle');
        
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $this->assertResponseRedirects('/tasks');
        $client->followRedirect();
        $this->assertSelectorExists('.alert.alert-success');
    }

    protected function tearDown(): void
    {
        $this->runCommand('doctrine:database:drop --force');

        parent::tearDown();

    }
}