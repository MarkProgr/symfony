<?php

namespace App\DataFixtures;

use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ProductFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        for ($i = 0; $i <= 10; ++$i) {
            $product = new Product();
            $product->setDescription(rand(0, 1000) . ' this product' . rand(1000, 2000));
            $product->setName('product' . $i);
            $product->setPrice(rand(0, 10000));
            $product->setManufacturer('Apple');
            $manager->persist($product);
        }

        $manager->flush();
    }
}
