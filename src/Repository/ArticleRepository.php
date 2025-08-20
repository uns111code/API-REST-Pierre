<?php

namespace App\Repository;

use App\Dto\Filter\ArticleFilterDto;
use App\Entity\Article;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Article>
 */
class ArticleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Article::class);
    }

    public function countAll(): int
    {
        return $this->createQueryBuilder('a')
            ->select('COUNT(a.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function findPaginate(ArticleFilterDto $filter): array
    {
        $offset = ($filter->getPage() - 1) * $filter->getLimit();

        $query = $this->createQueryBuilder('a')
            ->setMaxResults($filter->getLimit())
            ->setFirstResult($offset);

        $total = $this->countAll();

        return [
            'items' => $query->getQuery()->getResult(),
            'meta' => [
                'pages' => ceil($total / $filter->getLimit()),
                'total' => $total,
            ]
        ];
    }

    //    /**
//     * @return Article[] Returns an array of Article objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('a.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

    //    public function findOneBySomeField($value): ?Article
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
