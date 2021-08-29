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

        // Command created to link userless tasks to the anonymous user
        $this->runCommand('app:link-anonymous');        
    }

    public function getEntity($username) {
        $user = static::getContainer()->get('doctrine')->getManager()->getRepository(User::class)->findOneBy(['username' => $username]);
        return $user;
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
            $crawler->filter('.card')->count()
        );
    }

    public function testDisplayCreateTaskFormAuthorized()
    {
        $client = static::createClient();
        
        $user = $this->getEntity('testAdmin');
        $this->login($client, $user);

        $client->request('GET', '/tasks/create');

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertSelectorTextContains('h1', 'Créer une nouvelle tâche');
    }

    public function testDisplayCreateTaskFormUnAuthorized()
    {
        $client = static::createClient();
        
        $client->request('GET', '/tasks/create');

        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $client->followRedirect();
        $this->assertRouteSame('app_login');
    }

    public function testCreateTask()
    {
        $client = static::createClient();

        $user = $this->getEntity('testAdmin');
        $this->login($client, $user);

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

    public function testDisplayEditTaskFormAuthorized()
    {
        $client = static::createClient();

        $user = $this->getEntity('testUser');
        $this->login($client, $user);

        $client->request('GET', '/tasks/1/edit');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK); 
        // Add what it should display
        $this->assertSelectorTextContains('h1', 'Modifier cette tâche');
    }

    public function testDisplayEditTaskFormUnAuthorized()
    {
        $client = static::createClient();

        $client->request('GET', '/tasks/1/edit');
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND); 
        $client->followRedirect();
        $this->assertRouteSame('app_login');
    }

    public function testEditTask()
    {
            $client = static::createClient();
    
            $user = $this->getEntity('testUser');
            $this->login($client, $user);

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

        $client->request('GET', '/tasks/12/delete');

        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $client->followRedirect();
        $this->assertSelectorExists('.alert.alert-success');
    }

    public function testAuthorDeleteTask()
    {
        $client = static::createClient();

        $user = $this->getEntity('testUser');
        $this->login($client, $user);

        $client->request('GET', '/tasks/12/delete');

        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $client->followRedirect();
        $this->assertRouteSame('task_list');
        $this->assertSelectorExists('.alert.alert-success');
    }

    public function testNotAuthorDeleteTask()
    {
        $client = static::createClient();

        $user = $this->getEntity('Anonymous');
        $this->login($client, $user);

        $client->request('GET', '/tasks/4/delete');

        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $client->followRedirect();
        $this->assertRouteSame('task_list');
        $this->assertSelectorExists('.alert.alert-danger');
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

        $user = $this->getEntity('testUser');
        $this->login($client, $user);

        $client->request('GET', '/tasks/4/toggle');
        
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $client->followRedirect();
        $this->assertRouteSame('task_list');
        $this->assertSelectorExists('.alert.alert-success');
    }

    public function testToggleTaskNotAuthorized()
    {
        $client = static::createClient();

        $client->request('GET', '/tasks/4/toggle');

        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $client->followRedirect();
        $this->assertRouteSame('app_login');
    }

    protected function tearDown(): void
    {
        $this->runCommand('doctrine:database:drop --force');

        parent::tearDown();

    }
}