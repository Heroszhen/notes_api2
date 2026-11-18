<?php

declare(strict_types=1);

namespace App\Provider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\Pagination\Pagination;
use ApiPlatform\State\Pagination\TraversablePaginator;
use ApiPlatform\State\ProviderInterface;
use App\Dto\VideoOutput as DtoVideoOutput;
use App\Entity\Actress;
use App\Enum\VideoTypeEnum;
use App\Repository\ActressRepository;
use App\Service\S3Service;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

final class JasmineVideosProvider implements ProviderInterface
{
    private const VIDEO_LIMIT = 10;

    public function __construct(
        private readonly S3Service $s3Service,
        private readonly Pagination $pagination,
        private readonly ActressRepository $actressRepository,
        private readonly CacheInterface $cache,
        #[Autowire('%env(S3_ROOT_JASMINE)%')] private readonly string $s3RootJasmine,
        #[Autowire('%env(ACTRESS_ID_JASMINE)%')] private readonly string $actressIdJasmine,
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): TraversablePaginator
    {
        $page = $this->pagination->getPage($context);

        $videos = $this->cache->get('jasmine_videos', function (ItemInterface $item): array {
            $item->expiresAfter(300);

            $result = $this->s3Service->getFolder('videos', $this->s3RootJasmine);
            if (!isset($result['hydra:member'])) {
                throw new \RuntimeException('No videos');
            }

            return $result['hydra:member'];
        });

        $filters = $context['filters'] ?? [];
        $order = $filters['order'] ?? [];
        $sort = strtolower($order['createdAt'] ?? 'desc');

        $total = count($videos);
        if ('desc' === $sort) {
            usort($videos, fn (array $a, array $b): int => strcmp($b['updated'], $a['updated']));
        } else {
            usort($videos, fn (array $a, array $b): int => strcmp($a['updated'], $b['updated']));
        }

        $offset = ($page - 1) * self::VIDEO_LIMIT;
        $videos = array_slice($videos, $offset, self::VIDEO_LIMIT);

        $actress = $this->actressRepository->find($this->actressIdJasmine);
        if (!$actress instanceof Actress) {
            throw new \RuntimeException('No actress');
        }

        $tab = [];
        foreach ($videos as $video) {
            $tab[] = new DtoVideoOutput(
                id: $video['name'],
                title: '',
                videoUrl: $video['url'],
                videoType: VideoTypeEnum::MP4,
                website: 'https://www.google.com',
                photo: $actress->getPhoto(),
                actress: $actress
            );
        }

        return new TraversablePaginator(new \ArrayIterator($tab), $page, self::VIDEO_LIMIT, $total);
    }
}
