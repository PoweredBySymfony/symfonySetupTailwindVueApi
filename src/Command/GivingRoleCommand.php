<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;

#[AsCommand(
    name: 'giving:role',
    description: 'Giving a specific role to the user',
)]
class GivingRoleCommand extends Command
{
    private UserRepository $userRepository;
    private EntityManagerInterface $entityManager;

    public function __construct(UserRepository $userRepository, EntityManagerInterface $entityManager)
    {
        parent::__construct();
        $this->userRepository = $userRepository;
        $this->entityManager = $entityManager;
    }

    protected function configure(): void
    {
        $this
            ->addArgument('login', InputArgument::REQUIRED, 'The login of the user')
            ->addArgument('role', InputArgument::REQUIRED, 'The role to add (e.g., ROLE_ADMIN, ROLE_ORGANIZER)');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $login = $input->getArgument('login');
        $role = strtoupper($input->getArgument('role'));

        $user = $this->userRepository->findOneBy(['login' => $login]);

        if (!$user) {
            $output->writeln('<error>User not found.</error>');
            return Command::FAILURE;
        }

        $roles = $user->getRoles();

        // Vérifie si le rôle est déjà attribué
        if (in_array($role, $roles, true)) {
            $output->writeln('<comment>The user already has this role.</comment>');
            return Command::SUCCESS;
        }

        // Ajoute uniquement le rôle spécifique sans la hiérarchie
        $roles[] = $role;
        $user->setRoles(array_unique($roles));
        $this->entityManager->flush();

        $output->writeln('<info>Role added successfully!</info>');

        return Command::SUCCESS;
    }
}
