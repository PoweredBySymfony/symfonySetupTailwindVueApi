<?php

namespace App\State;

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
        #[Autowire(service: 'api_platform.doctrine.orm.state.persist_processor')] private ProcessorInterface $persistProcessor,
        private UserPasswordHasherInterface $userPasswordHasher,
    ){}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): mixed
    {
        // Handle the state
        // Vérifier que le mot de passe transmis ne soit pas null
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
