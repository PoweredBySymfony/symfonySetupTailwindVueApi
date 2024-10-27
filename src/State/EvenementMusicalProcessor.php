<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class EvenementMusicalProcessor implements ProcessorInterface
{
    public function __construct(private EntityManagerInterface $entityManager){}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): void
    {
        if ($data->getDateDeDebut() >= $data->getDateDeFin()) {
            throw new \InvalidArgumentException('La date de début doit être avant la date de fin.');
        }

        $this->entityManager->persist($data);
        $this->entityManager->flush();
    }
}
