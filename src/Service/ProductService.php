<?php

namespace App\Service;

use App\Entity\Product;

class ProductService
{
    public function getProducts(): array
    {
        $products = [];
        
        $iphone = new Product();
        $iphone->setName('Iphone')
               ->setPrice(100);
        $products[1] = $iphone;
        
        $headphones = new Product();
        $headphones->setName('Наушники')
                   ->setPrice(20);
        $products[2] = $headphones;
        
        $case = new Product();
        $case->setName('Чехол')
             ->setPrice(10);
        $products[3] = $case;
        
        return $products;
    }
    
    public function findById(int $id): ?Product
    {
        $products = $this->getProducts();
        
        return $products[$id] ?? null;
    }
} 