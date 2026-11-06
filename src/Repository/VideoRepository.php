<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Video;
use App\Model\VideoSearchModel;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Video>
 */
class VideoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Video::class);
    }

    public function findNextVideo(Video $video): ?Video
    {
        return $this->createQueryBuilder('video')
            ->Where('video.createdAt <= :createdAt')
            ->andWhere('video.id != :id')
            ->setParameter('createdAt', $video->getCreatedAt())
            ->setParameter('id', $video->getId())
            ->orderBy('video.createdAt', 'DESC')
            ->addOrderBy('video.id', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findPreviousVideo(Video $video): ?Video
    {
        return $this->createQueryBuilder('video')
            ->Where('video.createdAt >= :createdAt')
            ->andWhere('video.id != :id')
            ->setParameter('createdAt', $video->getCreatedAt())
            ->setParameter('id', $video->getId())
            ->orderBy('video.createdAt', 'ASC')
            ->addOrderBy('video.id', 'ASC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findBySearchForm(?VideoSearchModel $model = null): QueryBuilder
    {
        $qb = $this->createQueryBuilder('video')
            ->leftJoin('video.actress', 'actress')
            ->orderBy('video.createdAt', 'DESC');

        if (null === $model) {
            return $qb;
        }

        $qb
            ->Where(
                $qb->expr()->orX(
                    'video.title LIKE :title',
                    'video.description LIKE :description'
                )
            )
            ->setParameter('title', "%{$model->getKeywords()}%")
            ->setParameter('description', "%{$model->getKeywords()}%");

        if (!empty($model->getActress())) {
            $qb
                ->andWhere('video.actress = :actress')
                ->setParameter('actress', $model->getActress());
        }

        if (!empty($model->getVideoType())) {
            $qb
                ->andWhere('video.videoType = :videoType')
                ->setParameter('videoType', $model->getVideoType());
        }

        if (!empty($model->getWebsite())) {
            $qb
                ->andWhere('video.website LIKE :website')
                ->setParameter('website', "%{$model->getWebsite()}%");
        }

        return $qb;
    }
}
