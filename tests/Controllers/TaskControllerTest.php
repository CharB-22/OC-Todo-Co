<?php

namespace App\Tests\Controllers;

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
    }

    public function testCreateTask()
    {
        $client = static::createClient();
        $client->request('GET', '/tasks/create');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK); 
    }

}