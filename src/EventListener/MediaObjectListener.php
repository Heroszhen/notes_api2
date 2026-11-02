<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Entity\MediaObject;
use App\Service\S3Service;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Events;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

#[AsEntityListener(event: Events::postRemove, entity: MediaObject::class)]
final readonly class MediaObjectListener
{
    public function __construct(
        private S3Service $s3Service,
        #[Autowire(env: 'S3_ROOT')] private readonly string $s3Root,
    ) {
    }

    public function postRemove(MediaObject $mediaObject): void
    {
        if ($mediaObject->getName()) {
            $this->s3Service->deleteFile($mediaObject->getName(), $this->s3Root);
        }
    }
}
