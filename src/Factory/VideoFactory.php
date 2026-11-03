<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\Video;
use App\Enum\VideoTypeEnum;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/**
 * @extends PersistentObjectFactory<Video>
 */
final class VideoFactory extends PersistentObjectFactory
{
    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services
     *
     * @todo inject services if required
     */
    public function __construct()
    {
    }

    #[\Override]
    public static function class(): string
    {
        return Video::class;
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     *
     * @todo add your default values here
     */
    #[\Override]
    protected function defaults(): array|callable
    {
        return [
            'title' => self::faker()->sentence(4),
            'actress' => ActressFactory::new(),
            'description' => self::faker()->paragraph(),
            'videoType' => VideoTypeEnum::EXTERN,
            'videoUrl' => 'https://'.self::faker()->domainName().'/videos/'.self::faker()->uuid().'.mp4',
            'website' => 'https://'.self::faker()->domainName(),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    #[\Override]
    protected function initialize(): static
    {
        return $this
            // ->afterInstantiate(function(Video $video): void {})
        ;
    }
}
