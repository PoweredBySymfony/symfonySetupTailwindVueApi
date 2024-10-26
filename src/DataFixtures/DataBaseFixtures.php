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

        // Créer un pool de 10 utilisateurs avec des rôles aléatoires et les stocker dans un tableau
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

        // Créer un pool de 5 scènes et les stocker dans un tableau
        $scenes = [];
        for ($j = 0; $j < 5; $j++) {
            $scene = new Scene();
            $scene->setNom('Scene ' . $j);
            $scene->setNombreMaxParticipants(rand(100, 1000));
            $scenes[] = $scene;
            $manager->persist($scene);
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

            // Associer des utilisateurs aléatoires à cet événement musical
            foreach (array_slice($users, 0, rand(2, 5)) as $user) {
                $user->addEvenementMusical($evenementMusical);
            }

//            // Utiliser des scènes du pool existant
//            foreach ($scenes as $scene) {
//                $scene->setEvenementMusical($evenementMusical);
//
//                // Créer des parties de concert avec des utilisateurs en référence
//                for ($k = 0; $k < 2; $k++) { // Réduit à 2 parties par scène pour limiter la complexité
//                    $partieConcert = new PartieConcert();
//                    $partieConcert->setNom($faker->sentence(3));
//                    $randomUser = $this->getReference("user_" . rand(0, count($users) - 1));
//                    $partieConcert->setArtiste($randomUser);
//                    $partieConcert->setArtistePrincipal($faker->boolean);
//                    $partieConcert->addScene($scene);
//                    $partieConcert->setDateDeDebut(new \DateTime());
//                    $partieConcert->setDateDeFin(new \DateTime('+1 hour'));
//
//                    $manager->persist($partieConcert);
//                }
//            }

            $manager->persist($evenementMusical);
        }

        $manager->flush();
    }
}
