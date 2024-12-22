<?php

namespace App\DataFixtures;

use App\Entity\EvenementMusical;
use App\Entity\GenreMusical;
use App\Entity\PartieConcert;
use App\Entity\Scene;
use App\Entity\User;
use App\Entity\Ville;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Exception;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class DataBaseFixtures extends Fixture
{
    public function __construct(private readonly UserPasswordHasherInterface $passwordEncoder)
    {
    }

    /**
     * @throws Exception
     */
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        // Création des villes
        $villes = [];
        for ($i = 0; $i < 20; $i++) {
            $ville = new Ville();
            $ville->setNom($faker->city);
            $ville->setCodePostal($faker->postcode);
//            departement comme 13 Bouche du Rhone
            $ville->setPays($faker->randomElement(['France', 'Belgique', 'Suisse', 'Espagne', 'Italie', 'Canada']));
            if ($ville->getPays() == 'France') {
//                utilise provider fr adresse
                $ville->setDepartement($faker->departmentName);
            }

            $villes[] = $ville;
            $manager->persist($ville);
        }

        // Création des utilisateurs
        $participants = [];
        for ($i = 0; $i < 10; $i++) {
            $participant = new User();
            $participant->setNom($faker->firstName);
            $participant->setPrenom($faker->lastName);
            $participant->setEmail($faker->email);
            $participant->setRoles(['ROLE_USER']);
            $participant->setLogin($faker->unique()->userName);
            $participant->setVilleHabitation($faker->randomElement($villes));
            $participant->setDateDeNaissance($faker->dateTimeBetween('-50 years', '-18 years'));
            $participant->setPassword($this->passwordEncoder->hashPassword($participant, 'password'));
            $participants[] = $participant;

            $manager->persist($participant);
        }

        // Genres musicaux disponibles
        $genresMusicaux = ['Rock', 'Metal', 'Rap', 'Pop Rock', 'Jazz', 'Blues', 'Classique', 'Electro', 'Reggae', 'Hip Hop'];

        // Création des événements musicaux
        for ($i = 0; $i < 10; $i++) {
            $evenementMusical = new EvenementMusical();
            $evenementMusical->setNom('Evenement ' . ($i + 1));
            $evenementMusical->setDateDeDebut(new DateTime());
            $evenementMusical->setDateDeFin(new DateTime('+1 day'));
            $evenementMusical->setPrix($faker->randomFloat(2, 10, 100));
            $evenementMusical->setAdresse($faker->address);

            // Ajouter des participants à cet événement
            $numParticipants = rand(3, 6); // De 3 à 6 participants par événement
            for ($j = 0; $j < $numParticipants; $j++) {
                $participant = $participants[array_rand($participants)];
                $evenementMusical->addParticipant($participant);
            }

            // Créer des scènes pour cet événement
            for ($j = 0; $j < 3; $j++) {
                $scene = new Scene();
                $scene->setNom('Scene ' . ($j + 1));
                $scene->setNombreMaxParticipants(rand(50, 500));
                $scene->setEvenementMusical($evenementMusical);

                // Créer des parties de concert pour chaque scène
                for ($k = 0; $k < 5; $k++) {
                    $partieConcert = new PartieConcert();
                    $partieConcert->setNom($faker->sentence(3));
                    $partieConcert->setDateDeDebut(new DateTime());
                    $partieConcert->setDateDeFin(new DateTime('+1 hour'));
                    $partieConcert->setScene($scene);

                    // Créer un nouvel utilisateur **artiste** pour cette partie de concert
                    $artiste = new User();
                    $artiste->setNom($faker->firstName);
                    $artiste->setPrenom($faker->lastName);
                    $artiste->setEmail($faker->unique()->email);
                    $artiste->setRoles(['ROLE_ARTIST']);
                    $artiste->setLogin($faker->unique()->userName);
                    $artiste->setVilleHabitation($faker->randomElement($villes));
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
