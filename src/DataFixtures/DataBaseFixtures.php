<?php

namespace App\DataFixtures;

use App\Entity\EvenementMusical;
use App\Entity\PartieConcert;
use App\Entity\Scene;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class DataBaseFixtures extends Fixture
{
    public function __construct(private readonly UserPasswordHasherInterface $passwordEncoder)
    {
    }

    /**
     * @throws \Exception
     */
    public function load(ObjectManager $manager): void
    {
        $roles = ['ROLE_USER', 'ROLE_ADMIN', 'ROLE_ORGANIZER', 'ROLE_ARTIST'];

        // Création des utilisateurs
        foreach ($roles as $role) {
            for ($i = 1; $i <= 10; $i++) {
                $user = new User();
                $hashedPassword = $this->passwordEncoder->hashPassword($user, 'password' . $i);
                $user->setLogin($role . 'user' . $i)
                    ->setPassword($hashedPassword)
                    ->setEmail($role . 'user' . $i . '@example.com')
                    ->setNom('Nom ' . $role . ' ' . $i)
                    ->setPrenom('Prénom ' . $role . ' ' . $i)
                    ->setVilleHabitation('Ville ' . $i)
                    ->setDateDeNaissance(new \DateTime('-' . (20 + $i) . ' years'))
                    ->setRoles([$role]);

                $manager->persist($user);
            }
        }

        // Création d'événements musicaux pour chaque utilisateur
        for ($j = 1; $j <= 3; $j++) {
            $evenementMusical = new EvenementMusical();
            $evenementMusical->setNom('Événement Musical ' . $j . ' de ' . $user->getLogin())
                ->setDateDeDebut(new \DateTime('+ ' . $j . ' days'))
                ->setDateDeFin(new \DateTime('+ ' . ($j + 1) . ' days'))
                ->setPrix(rand(0, 100)) // Prix aléatoire
                ->setAdresse($user->getVilleHabitation() . ', ' . $user->getLogin() . ' Street')
                ->addParticipant($user);

            $manager->persist($evenementMusical);

            // Création de parties de concert pour chaque événement
            for ($k = 1; $k <= 2; $k++) {
                $partieConcert = new PartieConcert();
                $partieConcert->setNom('Partie de Concert ' . $k)
                    ->setArtistePrincipal($k === 1)
                    ->setDateDeDebut(new \DateTime('+ ' . $k . ' hours'))
                    ->setDateDeFin(new \DateTime('+ ' . ($k + 1) . ' hours'))
                    ->setArtiste($user); // Associe l'artiste

                $manager->persist($partieConcert);

                // Création de scènes pour chaque partie de concert
                for ($l = 1; $l <= 2; $l++) {
                    $scene = new Scene();
                    $scene->setNom('Scène ' . $l . ' de ' . $partieConcert->getNom())
                        ->setNombreMaxParticipants(rand(50, 200)) // Nombre maximum de participants aléatoire
                        ->setEvenementMusical($evenementMusical)
                        ->setPartieConcerts($partieConcert);

                    $manager->persist($scene);
                }
            }
        }
        $manager->flush();
    }
}
