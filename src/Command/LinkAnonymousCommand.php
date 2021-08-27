<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use App\Repository\TaskRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;

class LinkAnonymousCommand extends Command
{
    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'app:link-anonymous';

    public function __construct(EntityManagerInterface $entityManager, TaskRepository $taskRepository, UserRepository $userRepository)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
        $this->taskRepository = $taskRepository;
        $this->userRepository = $userRepository;
    }

    protected function configure(): void
    {
        $this->setDescription('Link former tasks without users to the anonymous user.');
    }


    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // code to link the userless-task to the anonymous user.
        // Get the lists of tasks not linked to a user
        $userlessTasks = $this->taskRepository->findBy(["user" => null]);
        // Get the "fake" anonymous user
        $anonymousUser = $this->userRepository->findOneBy(["username" => "Anonymous"]);
        $output->writeln([
            'Link tasks to anonymous',
            '=======================',
            '',
        ]);
        foreach ($userlessTasks as $task) {
            $task->setUser($anonymousUser);
            $this->entityManager->persist($task);
        }
        $this->entityManager->flush();
        $output->writeln("All userless tasks are link to anonymous user.");
        // this method must return an integer number with the "exit status code"
        // of the command. You can also use these constants to make code more readable

        // return this if there was no problem running the command
        // (it's equivalent to returning int(0))
        return Command::SUCCESS;

        // or return this if some error happened during the execution
        // (it's equivalent to returning int(1))
        // return Command::FAILURE;

        // or return this to indicate incorrect command usage; e.g. invalid options
        // or missing arguments (it's equivalent to returning int(2))
        // return Command::INVALID
    }
}