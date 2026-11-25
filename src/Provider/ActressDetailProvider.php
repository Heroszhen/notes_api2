<?php

declare(strict_types=1);

namespace App\Provider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Dto\ActressDetailOutput;
use App\Entity\Actress;
use App\Entity\ActressPhoto;
use App\Entity\Video;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

/**
 * @implements ProviderInterface<ActressDetailOutput>
 */
final class ActressDetailProvider implements ProviderInterface
{
    /**
     * @param ProviderInterface<object> $itemProvider
     */
    public function __construct(
        #[Autowire(service: 'api_platform.doctrine.orm.state.item_provider')]
        private readonly ProviderInterface $itemProvider,
        private readonly Security $security,
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): ?ActressDetailOutput
    {
        $actress = $this->itemProvider->provide($operation, $uriVariables, $context);
        if (!$actress instanceof Actress) {
            return null;
        }

        $isAdmin = $this->security->isGranted('ROLE_ADMIN');

        $videos = $actress->getVideos()
            ->filter(fn (Video $video) => $isAdmin || $video->getVisible())
            ->getValues();

        $photos = $actress->getImages()
            ->filter(fn (ActressPhoto $photo) => $isAdmin || $photo->getVisible())
            ->map(fn (ActressPhoto $photo) => $photo->getPhoto()->getName() ?? '')
            ->getValues();

        return new ActressDetailOutput(
            id: $actress->getId(),
            name: $actress->getName(),
            country: $actress->getCountry(),
            birthday: $actress->getBirthday(),
            description: $actress->getDescription(),
            photo: $actress->getPhoto()?->getName(),
            videos: $videos,
            images: $photos,
        );
    }
}
