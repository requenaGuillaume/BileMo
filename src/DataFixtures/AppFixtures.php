<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\User;
use DateTimeImmutable;
use App\Entity\Company;
use App\Entity\Product;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        for($i = 1; $i < 15; $i++){
            $product = new Product();

            $product->setBrand($faker->word())
                ->setModel($faker->name())
                ->setPriceHtInCents($faker->numberBetween(30000, 200000))
            ;

            $manager->persist($product);
        }


        for($c = 1; $c < 10; $c++){
            $company = new Company();

            $company->setName($faker->word());

            for($u = 1; $u < 10; $u++){
                $user = new User();

                $user->setCompany($company)
                    ->setLastName($faker->lastName())
                    ->setFirstName($faker->firstName())
                    ->setEmail($faker->email())
                    ->setPhoneNumber($faker->phoneNumber())
                    ->setCreatedAt(DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-1 week', '+1 week')))
                ;

                $company->addUser($user);

                $manager->persist($user);
            }

            $manager->persist($company);
        }

        $manager->flush();
    }
}
