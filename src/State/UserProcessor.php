<?php

namespace App\State;

use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserProcessor implements ProcessorInterface
{

    public function __construct(
        //Injection du service UserPasswordHasherInterface
        #[Autowire(service: 'api_platform.doctrine.orm.state.persist_processor')] private readonly ProcessorInterface $persistProcessor,
        private readonly UserPasswordHasherInterface $userPasswordHasher,
        private readonly EntityManagerInterface $entityManager,
    ){}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): mixed
    {

        // Vérification du type d'opération
//        $user = $data['user'];
        if ($operation instanceof Delete) {
            // Si l'opération est une suppression, dissocier des événements avant suppression
            foreach ($data->getEvenementMusicals() as $evenementMusical) {
                $evenementMusical->removeParticipant($data);
            }

            foreach ($data->getOrganisateurEvenementMuscial() as $evenementOrganise) {
                $evenementOrganise->setOrganisateur(null);
            }

            // Supprimer l'utilisateur de la base de données
            $this->entityManager->remove($data);
            $this->entityManager->flush();
            return null;
        }

        if ($data->getPlainPassword() !== null) {
            // Hacher le mot de pass
            $hashed = $this->userPasswordHasher->hashPassword($data, $data->getPlainPassword());
            //Modification des données de $data
            $data->setPassword($hashed);
        }
        // supprimer les informations sensibles
        $data->eraseCredentials();
        //Sauvegarde en base
        return $this->persistProcessor->process($data, $operation, $uriVariables, $context);
    }
}
