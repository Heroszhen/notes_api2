<?php

declare(strict_types=1);

namespace App\Doctrine;

use ApiPlatform\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;
use App\Entity\Video;
use Doctrine\ORM\QueryBuilder;

final class VisibleVideoExtension implements QueryCollectionExtensionInterface
{
    public function applyToCollection(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, ?Operation $operation = null, array $context = []): void
    {
        if (Video::class === $resourceClass && 'videos_public' === $operation?->getName()) {
            $queryBuilder->andWhere($queryBuilder->getRootAliases()[0].'.visible = true');
        }
    }
}
