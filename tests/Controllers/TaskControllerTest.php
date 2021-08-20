<?php

namespace App\Tests\Controllers;

use App\Entity\Task;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class TaskControllerTest extends WebTestCase 
{
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
        $client->request('GET', '/tasks/13/edit');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK); 
        // Add what it should display
        $this->assertSelectorTextContains('h1', 'Modifier cette tâche');
    }

    public function testEditTask()
    {
            $client = static::createClient();
    
            $crawler = $client->request('GET', '/tasks/13/edit');
            $form = $crawler->selectButton('Modifier')->form();
            $form['task[title]'] = 'Tâche nouvellement créer';
            $client->submit($form);
            
            $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
            $this->assertResponseRedirects('/tasks');
            $client->followRedirect();
            $this->assertSelectorExists('.alert.alert-success');
    }


    // Test toggle action
    public function testToggleAction()
    {
        $client = static::createClient();
        $client->request('GET', '/tasks/15/toggle');

        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $this->assertResponseRedirects('/tasks');
        $client->followRedirect();
        $this->assertSelectorExists('.alert.alert-success');

    }

    // Test delete action
    public function testDeleteTask()
    {
        $client = static::createClient();
        $client->request('GET', '/tasks/15/delete');

        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $this->assertResponseRedirects('/tasks');
        $client->followRedirect();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

}