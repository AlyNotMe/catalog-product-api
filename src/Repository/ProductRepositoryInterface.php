<?php

namespace App\Repository;

use App\Entity\Product;

interface ProductRepositoryInterface
{
    public function save(Product $product): void;

    public function findByReference(string $reference): ?Product;

    public function findActiveProducts(): array;
}
