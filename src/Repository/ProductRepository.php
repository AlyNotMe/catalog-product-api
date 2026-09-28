<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Product>
 */
class ProductRepository extends ServiceEntityRepository implements ProductRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

public function save(Product $product): void {
    $this->getEntityManager()->persist($product);
    $this->getEntityManager()->flush();
}

public function findByReference(string $reference): ?Product
{
    return $this->findOneBy(['reference' => $reference]);
}

public function findActiveProducts(): array
{
    return $this->findBy(['active' => true]);
}
}
