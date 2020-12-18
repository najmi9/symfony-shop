<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Product;
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

        for ($i=0; $i < 40; $i++) { 
            $product = new Product();
            $id  = random_int(10, 99);
            $image = "https://picsum.photos/id/$id/300/300";
            $product->setName($faker->productName())
                    ->setDescription($faker->paragraph(5))
                    ->setPrice($faker->randomFloat(1000, 10000))
                    ->setImage($image)
                    ->setCategory($faker->randomElement($categories))
            ;
            $manager->persist($product);
        }

        $manager->flush();
    }
}
