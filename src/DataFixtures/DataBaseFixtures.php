<?php

namespace App\DataFixtures;

use App\Entity\EvenementMusical;
use App\Entity\PartieConcert;
use App\Entity\Scene;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
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
        $faker = Factory::create();
        $roles = ['ROLE_USER', 'ROLE_ADMIN', 'ROLE_ORGANIZER', 'ROLE_ARTIST'];

        // Créer 10 utilisateurs de manière aléatoire
        $users = [];
        for ($j = 0; $j < 10; $j++) {
            $user = new User();
            $user->setNom($faker->firstName);
            $user->setPrenom($faker->lastName);
            $user->setEmail($faker->email);
            $user->setRoles([$roles[array_rand($roles)]]);
            $user->setLogin($faker->unique()->userName);
            $user->setVilleHabitation($faker->city);
            $user->setDateDeNaissance($faker->dateTimeBetween('-50 years', '-18 years'));
            $user->setPassword($this->passwordEncoder->hashPassword($user, 'password'));
            $users[] = $user;
            $this->addReference("user_$j", $user);
            $manager->persist($user);
        }

        // Créer 10 événements musicaux
        for ($i = 0; $i < 10; $i++) {
            $evenementMusical = new EvenementMusical();
            $evenementMusical->setNom('EvenementMusical ' . $i);
            $evenementMusical->setDateDeDebut(new \DateTime());
            $evenementMusical->setDateDeFin(new \DateTime('+1 day'));
            $evenementMusical->setPrix($faker->randomFloat(2, 0, 100));
            $evenementMusical->setAdresse($faker->address);
            $evenementMusical->setOrganisateur($this->getReference("user_" . rand(0, count($users) - 1)));

            // Ajouter un nombre aléatoire d'utilisateurs comme participants à l'événement
            $participants = array_slice($users, 0, rand(3, 6)); // De 3 à 6 participants
            foreach ($participants as $user) {
                $evenementMusical->addParticipant($user);
            }

            // Créer 3 scènes pour chaque événement
            $scenes = [];
            for ($j = 0; $j < 3; $j++) {
                $scene = new Scene();
                $scene->setNom('Scene ' . $j);
                $scene->setNombreMaxParticipants(rand(100, 1000));
                $scenes[] = $scene;
                $manager->persist($scene);
            }

            // Créer 5 parties de concert pour chaque scène
            foreach ($scenes as $scene) {
                for ($k = 0; $k < 5; $k++) {
                    $partieConcert = new PartieConcert();
                    $partieConcert->setNom($faker->sentence(3));
                    $partieConcert->setArtistePrincipal($faker->boolean); // Un artiste principal
                    $partieConcert->setScene($scene);
                    $partieConcert->setDateDeDebut(new \DateTime());
                    $partieConcert->setDateDeFin(new \DateTime('+1 hour'));

                    // Assigner des artistes (utilisateurs) à la partie de concert
                    $numArtistes = rand(1, 3); // 1 à 3 artistes par partie de concert
                    for ($l = 0; $l < $numArtistes; $l++) {
                        $artiste = $this->getReference("user_" . rand(0, count($users) - 1)); // Sélectionne un artiste aléatoire
                        $partieConcert->setArtiste($artiste); // Assigner l'artiste à la partie
                    }

                    $manager->persist($partieConcert);
                }
            }

            // Associer les scènes à l'événement musical
            foreach ($scenes as $scene) {
                $scene->setEvenementMusical($evenementMusical);
            }

            // Persister l'événement musical
            $manager->persist($evenementMusical);
        }

        $manager->flush();
    }
}
