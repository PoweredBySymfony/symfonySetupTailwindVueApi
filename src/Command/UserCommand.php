<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[AsCommand(
    name: 'create:user',
    description: 'Create a user',
)]
class UserCommand extends Command
{
    private EntityManagerInterface $entityManager;
    private UserPasswordHasherInterface $passwordHasher;
    private ValidatorInterface $validator;

    public function __construct(EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher, ValidatorInterface $validator)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
        $this->passwordHasher = $passwordHasher;
        $this->validator = $validator;
    }

    protected function configure(): void
    {
        $this
            ->addArgument('login', InputArgument::REQUIRED, 'The login of the user')
            ->addArgument('email', InputArgument::REQUIRED, 'The email of the user')
            ->addArgument('password', InputArgument::REQUIRED, 'The plain password of the user')
            ->addArgument('nom', InputArgument::REQUIRED, 'The last name of the user')
            ->addArgument('prenom', InputArgument::REQUIRED, 'The first name of the user')
            ->addArgument('villeHabitation', InputArgument::REQUIRED, 'The city of residence of the user')
            ->addArgument('dateDeNaissance', InputArgument::REQUIRED, 'The date of birth of the user (YYYY-MM-DD)');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $login = $input->getArgument('login');
        $email = $input->getArgument('email');
        $plainPassword = $input->getArgument('password');
        $nom = $input->getArgument('nom');
        $prenom = $input->getArgument('prenom');
        $villeHabitation = $input->getArgument('villeHabitation');
        $dateDeNaissance = \DateTime::createFromFormat('Y-m-d', $input->getArgument('dateDeNaissance'));

        $user = new User();
        $user->setLogin($login)
            ->setEmail($email)
            ->setNom($nom)
            ->setPrenom($prenom)
            ->setVilleHabitation($villeHabitation)
            ->setDateDeNaissance($dateDeNaissance);

        $user->setPassword($this->passwordHasher->hashPassword($user, $plainPassword));

        $errors = $this->validator->validate($user);

        if (count($errors) > 0) {
            foreach ($errors as $error) {
                $output->writeln('<error>' . $error->getMessage() . '</error>');
            }
            return Command::FAILURE;
        }

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $output->writeln('<info>User successfully created!</info>');

        return Command::SUCCESS;
    }

    protected function interact(InputInterface $input, OutputInterface $output): void
    {
        $helper = $this->getHelper('question');

        foreach (['login', 'email', 'password', 'nom', 'prenom', 'villeHabitation', 'dateDeNaissance'] as $argument) {
            if (!$input->getArgument($argument)) {
                $question = new Question(ucfirst($argument) . ': ');
                $input->setArgument($argument, $helper->ask($input, $output, $question));
            }
        }
    }
}
