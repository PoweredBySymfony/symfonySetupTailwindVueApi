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

        // Créer 10 utilisateurs aléatoires (uniquement pour les participants)
        $participants = [];
        for ($i = 0; $i < 10; $i++) {
            $participant = new User();
            $participant->setNom($faker->firstName);
            $participant->setPrenom($faker->lastName);
            $participant->setEmail($faker->email);
            $participant->setRoles(['ROLE_USER']);
            $participant->setLogin($faker->unique()->userName);
            $participant->setVilleHabitation($faker->city);
            $participant->setDateDeNaissance($faker->dateTimeBetween('-50 years', '-18 years'));
            $participant->setPassword($this->passwordEncoder->hashPassword($participant, 'password'));
            $participants[] = $participant;

            $manager->persist($participant);
        }

        // Créer des événements musicaux
        for ($i = 0; $i < 10; $i++) {
            $evenementMusical = new EvenementMusical();
            $evenementMusical->setNom('Evenement ' . ($i + 1));
            $evenementMusical->setDateDeDebut(new \DateTime());
            $evenementMusical->setDateDeFin(new \DateTime('+1 day'));
            $evenementMusical->setPrix($faker->randomFloat(2, 10, 100));
            $evenementMusical->setAdresse($faker->address);

            // Ajouter des participants à cet événement
            $numParticipants = rand(3, 6); // De 3 à 6 participants par événement
            for ($j = 0; $j < $numParticipants; $j++) {
                $participant = $participants[array_rand($participants)];
                $evenementMusical->addParticipant($participant);
            }

            // Créer des scènes pour cet événement
            for ($j = 0; $j < 2; $j++) {
                $scene = new Scene();
                $scene->setNom('Scene ' . ($j + 1));
                $scene->setNombreMaxParticipants(rand(50, 500));
                $scene->setEvenementMusical($evenementMusical);

                // Créer des parties de concert pour chaque scène
                for ($k = 0; $k < 3; $k++) {
                    $partieConcert = new PartieConcert();
                    $partieConcert->setNom($faker->sentence(3));
                    $partieConcert->setDateDeDebut(new \DateTime());
                    $partieConcert->setDateDeFin(new \DateTime('+1 hour'));
                    $partieConcert->setScene($scene);

                    // Créer un nouvel utilisateur **artiste** pour cette partie de concert
                    $artiste = new User();
                    $artiste->setNom($faker->firstName);
                    $artiste->setPrenom($faker->lastName);
                    $artiste->setEmail($faker->unique()->email);
                    $artiste->setRoles(['ROLE_ARTIST']);
                    $artiste->setLogin($faker->unique()->userName);
                    $artiste->setVilleHabitation($faker->city);
                    $artiste->setDateDeNaissance($faker->dateTimeBetween('-50 years', '-18 years'));
                    $artiste->setPassword($this->passwordEncoder->hashPassword($artiste, 'password'));

                    $manager->persist($artiste);

                    // Assigner cet artiste à la partie de concert
                    $partieConcert->setArtistePrincipal(true);
                    $partieConcert->setArtiste($artiste);

                    $manager->persist($partieConcert);
                }

                $manager->persist($scene);
            }

            $manager->persist($evenementMusical);
        }

        $manager->flush();
    }
}
