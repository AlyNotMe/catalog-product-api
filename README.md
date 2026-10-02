# Catalog Product API

Petit projet Symfony/PHP construit pour montrer une architecture à **inversion de dépendances** (DIP) : la logique métier ne dépend d'aucune base de données, elle dépend d'une interface. Résultat : les règles métier se testent en quelques millisecondes, sans base.

Même approche architecturale que [Forja](https://github.com/forjajs/forja) (mon framework Node.js), appliquée ici en PHP.

## Stack

- PHP >= 8.2, Symfony 7.4
- Doctrine ORM 3 (SQLite, aucun serveur à lancer)
- PHPUnit 11

## Lancer le projet

```bash
git clone https://github.com/AlyNotMe/catalog-product-api.git
cd catalog-product-api
composer install
php bin/console doctrine:migrations:migrate --no-interaction
php bin/phpunit
```

Résultat attendu : `OK (3 tests, 4 assertions)`.

## Architecture

```
src/
  Entity/
    Product.php                       Entité Doctrine (nom, référence unique, prix, actif)
  Repository/
    ProductRepositoryInterface.php    Le contrat : save, findByReference, findActiveProducts
    ProductRepository.php             Implémentation Doctrine du contrat
  Service/
    ProductCatalogService.php         Logique métier (guard clauses)
tests/
  Support/
    InMemoryProductRepository.php     Faux repository en mémoire (test double)
  Service/
    ProductCatalogServiceTest.php     Tests unitaires du service, sans base de données
```

### Le point central : le DIP

`ProductCatalogService` reçoit un `ProductRepositoryInterface` dans son constructeur, jamais la classe Doctrine concrète :

```php
public function __construct(
    private ProductRepositoryInterface $productRepository,
) {}
```

Conséquences concrètes :

- le service se teste avec `InMemoryProductRepository` (un simple tableau PHP), donc sans base ni setup
- changer de moteur de stockage ne demande aucune modification de la logique métier
- seule la couche `Repository/ProductRepository.php` connaît Doctrine

### Règles métier (`createProduct`)

Les vérifications se font dans cet ordre, avant de créer l'objet (fail-fast) :

1. la référence ne doit pas être vide (après `trim`)
2. le prix ne doit pas être négatif
3. la référence ne doit pas exister déjà (vérification via le repository, car c'est une information qui dépend de l'état du système et pas seulement de l'entrée)

La contrainte d'unicité existe aussi au niveau base (`UniqueConstraint` sur `reference`) : la règle est garantie à deux niveaux.

## Tests

```bash
php bin/phpunit
```

3 tests unitaires : création valide, rejet d'une référence dupliquée, rejet d'un prix négatif.

## Périmètre volontaire

Ce projet se concentre sur le backend et l'architecture :

- **pas de Controller / endpoint HTTP** pour l'instant : le service n'est pas encore exposé en API
- `Product` n'a que quatre champs ; les spécifications techniques filtrables (idée initiale d'un catalogue de matériaux) ne sont pas implémentées
- `compose.yaml` / `compose.override.yaml` viennent de la recette Doctrine de Symfony (PostgreSQL) et ne sont pas utilisés : le projet tourne en SQLite

## Pistes d'évolution

- [ ] Endpoints REST (`POST /products`, `GET /products`, `GET /products/{id}`)
- [ ] Spécifications techniques par produit et filtrage par plage de valeurs
- [ ] Tests fonctionnels sur les endpoints
