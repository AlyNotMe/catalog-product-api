<?php

namespace App\Tests\Service;

use App\Service\ProductCatalogService;
use App\Tests\Support\InMemoryProductRepository;
use PHPUnit\Framework\TestCase;

class ProductCatalogServiceTest extends TestCase
{
    public function testCreateProductSucceedsWithValidData(): void
    {
        $service = new ProductCatalogService(new InMemoryProductRepository());

        $product = $service->createProduct('Isolant thermique', 'ISO-2026-01', 45.90);

        $this->assertSame('Isolant thermique', $product->getName());
        $this->assertTrue($product->isActive());
    }

    public function testCreateProductRejectsDuplicateReference(): void
    {
        $repository = new InMemoryProductRepository();
        $service = new ProductCatalogService($repository);
        $service->createProduct('Produit A', 'REF-01', 10.0);

        $this->expectException(\InvalidArgumentException::class);

        $service->createProduct('Produit B', 'REF-01', 20.0);
    }

    public function testCreateProductRejectsNegativePrice(): void
    {
        $service = new ProductCatalogService(new InMemoryProductRepository());

        $this->expectException(\InvalidArgumentException::class);

        $service->createProduct('Produit', 'REF-02', -5.0);
    }
}
