<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Entity\Video;
use App\Service\S3Service;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Events;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

#[AsEntityListener(event: Events::postRemove, entity: Video::class)]
final readonly class VideoListener
{
    public function __construct(
        private S3Service $s3Service,
        #[Autowire(env: 'S3_ROOT_AUDIO')] private readonly string $s3RootAudio,
        #[Autowire(env: 'S3_BUCKET')] private readonly string $s3Bucket,
    ) {
    }

    public function postRemove(Video $video): void
    {
        if (empty($video->getVideoUrl())) {
            return;
        }

        if (!str_contains($video->getVideoUrl(), $this->s3Bucket)) {
            return;
        }

        $tab = explode('/', $video->getVideoUrl());
        $this->s3Service->deleteFile(end($tab), $this->s3RootAudio);
    }
}
