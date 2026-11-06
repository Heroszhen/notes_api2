<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Video;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
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
}
