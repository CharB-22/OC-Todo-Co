<?php

namespace App\Controller;

use App\Entity\Task;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TaskController extends AbstractController
{
    /**
     * @Route("/tasks", name="task_list")
     */
    public function listAction()
    {
        $taskList = $this->getDoctrine()->getRepository(Task::class)->findAll();
        return $this->render(
            'task/list.html.twig', 
            ['tasks' => $taskList]
        );
    }
}
