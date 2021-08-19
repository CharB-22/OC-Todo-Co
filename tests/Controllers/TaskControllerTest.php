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

    public function testDisplayEditTaskForm()
    {
        $client = static::createClient();
        $client->request('GET', '/tasks/13/edit');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK); 
        // Add what it should display
        $this->assertSelectorTextContains('h1', 'Modifier cette tâche');
    }

}