<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\User;
use Faker\Generator;
use App\Entity\Article;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private Generator $faker;

    public function __construct(
        private UserPasswordHasherInterface $passwordHasher
    )
    {
        $this->faker = Factory::create('fr_FR');
    }

    public function load(ObjectManager $manager): void
    {
        // Création d’un tableau pour stocker les utilisateurs
        $arrayUsers = [];

        // Création de l’admin
        $admin = new User();
        $admin
            ->setEmail('admin@admin.com')
            ->setPassword(
                $this->passwordHasher->hashPassword(
                    $admin,
                    'admin'
                )
            )
            ->setFirstName('admin')
            ->setLastName('admin');
        $admin->setRoles(['ROLE_ADMIN']);

        $manager->persist($admin);
        $arrayUsers[] = $admin;

        // Création de 15 utilisateurs
        for ($i = 0; $i < 15; $i++) {
            $user = new User();
            $user
                ->setEmail($this->faker->unique()->email())
                ->setPassword(
                    $this->passwordHasher->hashPassword(
                        $user,
                        'user'
                    )
                )
                ->setFirstName($this->faker->firstName())
                ->setLastName($this->faker->lastName());

            $manager->persist($user);
            $arrayUsers[] = $user; // 👍 On ajoute à la liste des utilisateurs
        }

        // Création de 15 articles liés à des utilisateurs aléatoires
        for ($i = 0; $i < 15; $i++) {
            $article = new Article();
            $article
                ->setTitle($this->faker->sentence(3))
                ->setSlug($this->faker->slug())
                ->setContent($this->faker->paragraph(5))
                ->setShortContent($this->faker->paragraph(2))
                ->setEnabled($this->faker->boolean())
                ->setUser($this->faker->randomElement($arrayUsers));

            $manager->persist($article);
        }

        $manager->flush();
    }
}
