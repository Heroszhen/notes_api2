<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\ActressPhoto;
use App\Model\ActressSearchModel;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ActressPhoto>
 */
class ActressPhotoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ActressPhoto::class);
    }

    //    /**
    //     * @return ActressPhoto[] Returns an array of ActressPhoto objects
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

    //    public function findOneBySomeField($value): ?ActressPhoto
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
        $qb = $this->createQueryBuilder('actressPhoto');

        $qb
            ->leftJoin('actressPhoto.actress', 'actress')
            ->Where('actress.name LIKE :name')
            ->setParameter('name', "%{$model->getKeywords()}%")
            ->orderBy('actressPhoto.createdAt', 'DESC')
            ->addOrderBy('actressPhoto.id', 'DESC');

        if (!empty($model->getCountry())) {
            $qb
                ->andWhere('actress.country = :country')
                ->setParameter('country', $model->getCountry());
        }

        return $qb;
    }
}
