<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Product;
use App\Entity\User;
use App\Entity\Like;
use App\Entity\Order;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $faker = \Faker\Factory::create();
        \Bezhanov\Faker\ProviderCollectionHelper::addAllProvidersTo($faker);
        $faker->addProvider(new \Bezhanov\Faker\Provider\Commerce($faker));

        $categories = [];

        for ($i=0; $i <5 ; $i++) { 
           $category = new Category();
        
           $category->setTitle($faker->department())
                    ->setDescription($faker->paragraph(4))
           ;
           $categories[] = $category;
           $manager->persist($category);
        }

        $products = [];

        for ($i=0; $i < 40; $i++) { 
            $product = new Product();
            $id  = random_int(10, 99);
            $image = "https://picsum.photos/id/$id/300/300";
            $product->setName($faker->productName())
                    ->setDescription($faker->paragraph(1))
                    ->setPrice(mt_rand(10, 100))
                    ->setImage($image)
                    ->setCategory($faker->randomElement($categories))
            ;
            $products[] = $product;

            $manager->persist($product);
        }

        $users = [];

        $user = new User();

        $user->setName('Admin')
            ->setEmail('admin@gmail.com')
            ->setRoles(['ROLE_USER', 'ROLE_ADMIN'])
            ->setPassword('$argon2id$v=19$m=65536,t=4,p=1$prGvlZFMQashHB1sff942Q$o1h12cbd7sc3JVBlniONE5xQ6rfDfkbbXMexqEK1AB4')
            ->setAddress('Marrakech')
            ->setCreatedAt($faker->dateTimeBetween('-1 year'))
            ->setUpdatedAt($faker->dateTimeBetween('-1 year'))
            ->setEnabled(true)
        ;

        $manager->persist($user);

        $users[] = $user;

        for ($i=0; $i < 25; $i++) { 
            $user = new User();

            $user->setName($faker->name)
                ->setAddress($faker->address)
                ->setEmail($faker->email)
                ->setCreatedAt($faker->dateTimeBetween('-1 year'))
                ->setUpdatedAt($faker->dateTimeBetween('-1 year'))  
                ->setEnabled($faker->randomElement([true, false]))       
            ;

            $users[] = $user;
            $manager->persist($user);
        }

        for ($i=0; $i < 50; $i++) { 
            $like = new Like();

            $like->setProduct($faker->randomElement($products))
                 ->setUser($faker->randomElement($users))
            ;

            $manager->persist($like);
        }

        for ($i=0; $i < 10; $i++) { 
           $order = new Order();

           $order->setIdentifiant(uniqid())
                ->setCreatedAt($faker->dateTimeBetween('-1 year'))
                ->setUpdatedAt($faker->dateTimeBetween('-1 year')) 
                ->setStatus($faker->randomElement(['CAPTURED', 'CREATED']))
                ->setUser($faker->randomElement($users))
                ->setAmount(mt_rand(10, 400))
                ->setApproveLink('link provided by paypal')
            ;

            $manager->persist($order);   
        }

        $manager->flush();
    }
}
