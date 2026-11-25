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
            ->setParameter('description', "%{$model->getKeywords()}%")
            ->orderBy('actress.createdAt', 'DESC');

        if (!empty($model->getCountry())) {
            $qb
                ->andWhere('actress.country = :country')
                ->setParameter('country', $model->getCountry());
        }

        return $qb;
    }

    /**
     * @return string[]
     */
    public function findCountries(): array
    {
        return $this->createQueryBuilder('actress')
            ->select('DISTINCT actress.country')
            ->orderBy('actress.country', 'ASC')
            ->getQuery()
            ->getSingleColumnResult();
    }
}
