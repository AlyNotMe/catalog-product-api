<?php

namespace App\Service;

use App\Entity\Product;
use App\Repository\ProductRepositoryInterface;

class ProductCatalogService
{
    public function __construct(
        private ProductRepositoryInterface $productRepository,
    ) {}

    public function createProduct(string $name, string $reference, float $price): Product
    {
        if (trim($reference) === '') {
            throw new \InvalidArgumentException('La reference produit ne peut pas etre vide.');
        }
        if ($price < 0) {
            throw new \InvalidArgumentException('Le prix ne peut pas etre negatif.');
        }

        if ($this->productRepository->findByReference($reference)) {
            throw new \InvalidArgumentException(sprintf('La reference "%s" existe deja.', $reference));
        }

        $product = new Product();
        $product->setName($name)->setReference($reference)->setPrice($price)->setActive(true);

        $this->productRepository->save($product);

        return $product;
    }
}
