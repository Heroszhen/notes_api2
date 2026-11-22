<?php

namespace App\Repository\Blog;

use App\Entity\Blog\Article;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
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

    public function findBySearchForm(): QueryBuilder
    {
        $qb = $this->createQueryBuilder('article')
            ->leftJoin('article.category', 'category')
            ->orderBy('video.createdAt', 'DESC');

        return $qb;
    }
}
