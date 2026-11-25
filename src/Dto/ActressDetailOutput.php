<?php

declare(strict_types=1);

namespace App\Dto;

use App\Entity\Video;
use Symfony\Component\Serializer\Attribute\Groups;

final class ActressDetailOutput
{
    /**
     * @param Video[]      $videos
     * @param list<string> $images noms des fichiers
     */
    public function __construct(
        #[Groups(['actress:detail'])] public string $id,
        #[Groups(['actress:detail'])] public ?string $name,
        #[Groups(['actress:detail'])] public ?string $country,
        #[Groups(['actress:detail'])] public ?\DateTimeImmutable $birthday,
        #[Groups(['actress:detail'])] public ?string $description,
        #[Groups(['actress:detail'])] public ?string $photo,
        #[Groups(['actress:detail'])] public array $videos,
        #[Groups(['actress:detail'])] public array $images,
    ) {
    }
}
