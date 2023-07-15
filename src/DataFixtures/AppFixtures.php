<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\User;
use DateTimeImmutable;
use App\Entity\Company;
use App\Entity\Product;
use App\Entity\SelfDiscoverability;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class AppFixtures extends Fixture
{
    private const PRODUCTS = [
        [
            'uri' => '/api/products',
            'method' => 'GET',
            'arguments' => [],
            'description' => 'Get the list of all products'
        ],
        [
            'uri' => '/api/products/{id}',
            'method' => 'GET',
            'arguments' => ['id' => 'Integer'],
            'description' => 'Get one product'
        ]
    ];

    private const USERS = [
        [
            'uri' => '/api/users/company/{company_id}',
            'method' => 'GET',
            'arguments' => ['company_id' => 'Integer'],
            'description' => 'Get the list of all users linked to a company'
        ],
        [
            'uri' => '/api/users/{user_id}/company/{company_id}',
            'method' => 'GET',
            'arguments' => ['user_id' => 'Integer', 'company_id' => 'Integer'],
            'description' => 'Get one user linked to a company'
        ],
        [
            'uri' => '/api/users/company/{company_id}',
            'method' => 'POST',
            'arguments' => ['company_id' => 'Integer'],
            'description' => 'Create a new user linked to a company'
        ],
        [
            'uri' => '/api/users/{user_id}/company/{company_id}',
            'method' => 'DELETE',
            'arguments' => ['user_id' => 'Integer', 'company_id' => 'Integer'],
            'description' => 'Delete an user linked to a company'
        ]
    ];

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

        foreach(self::PRODUCTS as $product){
            $selfDiscoverability = new SelfDiscoverability();
            $selfDiscoverability->setResource('products')
                ->setUri($product['uri'])
                ->setMethod($product['method'])
                ->setArguments($product['arguments'])
                ->setDescription($product['description']);

            $manager->persist($selfDiscoverability);
        }

        foreach(self::USERS as $user){
            $selfDiscoverability = new SelfDiscoverability();
            $selfDiscoverability->setResource('users')
                ->setUri($user['uri'])
                ->setMethod($user['method'])
                ->setArguments($user['arguments'])
                ->setDescription($user['description']);

            $manager->persist($selfDiscoverability);
        }

        $manager->flush();
    }
}
