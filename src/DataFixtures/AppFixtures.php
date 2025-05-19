<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Faker\Factory;
use Faker\Generator;
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
        // $product = new Product();
        // $manager->persist($product);

        $user = new User();
        $user
            ->setEmail('admin@admin.com')
            ->setPassword(
                $this->passwordHasher->hashPassword(
                    $user,
                    'admin'
                )
            )
            ->setFirstName('admin')
            ->setLastName('admin');
        $user->setRoles(['ROLE_ADMIN']);

        $manager->persist($user);


        for ($i = 0; $i <= 15; $i++) {
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
        }

        $manager->flush();
    }
}
