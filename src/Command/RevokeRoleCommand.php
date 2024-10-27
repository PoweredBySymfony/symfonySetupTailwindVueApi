<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
#[AsCommand(
    name: 'revoke:role',
    description: 'Remove role to the user',
)]
class RevokeRoleCommand extends Command
{
    private UserRepository $userRepository;
    private EntityManagerInterface $entityManager;

    // Définir la hiérarchie des rôles
    private const ROLE_HIERARCHY = [
        'ROLE_ADMIN' => ['ROLE_BASE'],
        'ROLE_ORGANIZER' => ['ROLE_BASE'],
        'ROLE_ARTIST' => ['ROLE_BASE'],
        'ROLE_BASE' => ['ROLE_USER'],
    ];

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
            ->addArgument('role', InputArgument::REQUIRED, 'The role to remove (e.g., ROLE_ADMIN, ROLE_ORGANIZER)');
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
        if (!in_array($role, $roles, true)) {
            $output->writeln('<comment>The user does not have this role.</comment>');
            return Command::SUCCESS;
        }

        // Supprimer le rôle et vérifier la hiérarchie pour voir si des rôles enfants doivent être retirés
        $roles = array_diff($roles, [$role]);
        foreach (self::ROLE_HIERARCHY as $parent => $children) {
            if ($role === $parent) {
                $roles = array_diff($roles, $children);
            }
        }
        $user->setRoles(array_values($roles));
        $this->entityManager->flush();

        $output->writeln('<info>Role and its hierarchical children removed successfully!</info>');

        return Command::SUCCESS;
    }
}
