<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Product>
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    //    /**
    //     * @return Product[] Returns an array of Product objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('p.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Product
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    // public function findAllRandom(int $limit = 9): array
    // {
    //     $conn = $this->getEntityManager()->getConnection();

    //     $sql = '
    //         SELECT p.* FROM product p
    //         ORDER BY RAND()
    //         LIMIT :limit
    //     ';

    //     $stmt = $conn->prepare($sql);
    //     $stmt->bindValue('limit', $limit, \PDO::PARAM_INT);
    //     $resultSet = $stmt->executeQuery();

    //     $results = $resultSet->fetchAllAssociative();

    //     // Transformer les résultats SQL bruts en entités
    //     return $this->getEntityManager()
    //                 ->getRepository(Product::class)
    //                 ->findBy(['id' => array_column($results, 'id')]);
    // }

    public function findAllRandom(int $limit = 8): array
    {
        $conn = $this->getEntityManager()->getConnection();

        // Étape 1 : récupérer uniquement les IDs
        $sql = 'SELECT id FROM product';
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();
        $ids = array_column($resultSet->fetchAllAssociative(), 'id');

        // Étape 2 : tirer des IDs au hasard côté PHP
        shuffle($ids); // mélanger
        $randomIds = array_slice($ids, 0, $limit); // garder $limit éléments

        if (empty($randomIds)) {
            return [];
        }

        // Étape 3 : récupérer les entités avec leurs catégories
        return $this->createQueryBuilder('p')
            ->leftJoin('p.category', 'c')
            ->addSelect('c')
            ->where('p.id IN (:ids)')
            ->setParameter('ids', $randomIds)
            ->getQuery()
            ->getResult();
    }


    public function findByCategoryName(string $name): array
    {
        return $this->createQueryBuilder('p')
            ->join('p.category', 'c')
            ->where('c.category = :name')
            ->setParameter('name', $name)
            ->getQuery()
            ->getResult();
    }
    
    // public function searchByKeyword(string $query): array
    // {
    //     return $this->createQueryBuilder('p')
    //         ->leftJoin('p.category', 'c')
    //         ->addSelect('c')
    //         ->where('p.titre LIKE :q OR p.description LIKE :q')
    //         ->setParameter('q', '%' . $query . '%')
    //         ->getQuery()
    //         ->getResult();
    // }

    public function searchByKeyword(string $keyword): array
    {
        $conn = $this->getEntityManager()->getConnection();

        // Préparer la recherche en mode BOOLEAN avec les mots séparés par espace
        // Ajouter un '*' pour recherche "wildcard" sur chaque mot
        $words = array_filter(preg_split('/\s+/', $keyword));
        $searchString = implode(' ', array_map(fn($w) => $w . '*', $words));

        $sql = "
            SELECT p.id
            FROM product p
            WHERE MATCH(p.titre, p.description) AGAINST (:search IN BOOLEAN MODE)
            ORDER BY MATCH(p.titre, p.description) AGAINST (:search IN BOOLEAN MODE) DESC
        ";

        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery(['search' => $searchString]);

        $ids = array_column($resultSet->fetchAllAssociative(), 'id');

        if (empty($ids)) {
            return [];
        }

        // Charger les entités Product correspondantes
        $products = $this->createQueryBuilder('p')
            ->leftJoin('p.category', 'c')
            ->addSelect('c')
            ->where('p.id IN (:ids)')
            ->setParameter('ids', $ids)
            ->getQuery()
            ->getResult();

        // Réordonner les entités pour respecter l'ordre des ids
        $productsById = [];
        foreach ($products as $product) {
            $productsById[$product->getId()] = $product;
        }

        $orderedProducts = [];
        foreach ($ids as $id) {
            if (isset($productsById[$id])) {
                $orderedProducts[] = $productsById[$id];
            }
        }

        return $orderedProducts;
    }
}
