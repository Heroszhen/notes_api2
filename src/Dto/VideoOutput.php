<?php

declare(strict_types=1);

namespace App\Dto;

use App\Entity\Actress;
use App\Entity\MediaObject;
use App\Enum\VideoTypeEnum;
use Symfony\Component\Serializer\Attribute\Groups;

#[Groups(['default', 'video:o'])]
class VideoOutput
{
    public function __construct(
        public string $id,
        public ?string $title = null,
        public ?string $description = null,
        public ?string $videoUrl = null,
        public VideoTypeEnum $videoType = VideoTypeEnum::EXTERN,
        public ?string $website = null,
        public ?MediaObject $photo = null,
        public ?Actress $actress = null,
    ) {
    }
}
