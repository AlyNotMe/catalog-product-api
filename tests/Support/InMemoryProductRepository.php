<?php

namespace App\Tests\Support;

use App\Entity\Product;
use App\Repository\ProductRepositoryInterface;

class InMemoryProductRepository implements ProductRepositoryInterface
{
    /** @var Product[] */
    private array $products = [];

    public function save(Product $product): void
    {
        $this->products[] = $product;
    }

    public function findByReference(string $reference): ?Product
    {
        foreach ($this->products as $product) {
            if ($product->getReference() === $reference) return $product;
        }
        return null;
    }


    public function findActiveProducts(): array
    {
        return array_values(array_filter($this->products, fn(Product $p) => $p->isActive()));
    }
}
