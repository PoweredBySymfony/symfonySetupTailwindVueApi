<?php

namespace App\State;

use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class UserProcessor implements ProcessorInterface
{
    public function __construct(
        #[Autowire(service: 'api_platform.doctrine.orm.state.persist_processor')] private readonly ProcessorInterface $persistProcessor,
        private readonly UserPasswordHasherInterface $userPasswordHasher,
        private readonly EntityManagerInterface $entityManager,
    ) {}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): mixed
    {
        if ($operation instanceof Delete) {
            foreach ($data->getEvenementMusicals() as $evenementMusical) {
                $evenementMusical->removeParticipant($data);
            }

            foreach ($data->getOrganisateurEvenementMuscial() as $evenementOrganise) {
                $evenementOrganise->setOrganisateur(null);
            }

            $this->entityManager->remove($data);
            $this->entityManager->flush();
            return null;
        }

        $dateDeNaissance = $data->getDateDeNaissance();
        if ($dateDeNaissance > new \DateTime()) {
            throw new \InvalidArgumentException("La date de naissance ne peut pas être une date future.");
        }

        if ($data->getPlainPassword() !== null) {
            $hashed = $this->userPasswordHasher->hashPassword($data, $data->getPlainPassword());
            $data->setPassword($hashed);
        }

        $data->eraseCredentials();
        return $this->persistProcessor->process($data, $operation, $uriVariables, $context);
    }
}
