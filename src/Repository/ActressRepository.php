<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Actress;
use App\Model\ActressSearchModel;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Actress>
 */
class ActressRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Actress::class);
    }

    //    /**
    //     * @return Actress[] Returns an array of Actress objects
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

    //    public function findOneBySomeField($value): ?Actress
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    public function findBySearchForm(ActressSearchModel $model): QueryBuilder
    {
        $qb = $this->createQueryBuilder('actress');

        $qb
            ->Where(
                $qb->expr()->orX(
                    'actress.name LIKE :name',
                    'actress.description LIKE :description'
                )
            )
            ->setParameter('name', "%{$model->getKeywords()}%")
            ->setParameter('description', "%{$model->getKeywords()}%");

        if (!empty($model->getCountry())) {
            $qb
                ->andWhere('actress.country = :country')
                ->setParameter('country', $model->getCountry());
        }

        return $qb;
    }
}
