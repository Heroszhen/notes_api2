<?php

declare(strict_types=1);

namespace App\Model;

use App\Entity\Actress;
use App\Enum\VideoTypeEnum;

final class VideoSearchModel extends SearchBarModel
{
    private ?Actress $actress = null;

    private ?VideoTypeEnum $videoType = null;

    private ?string $website = null;

    public function getActress(): ?Actress
    {
        return $this->actress;
    }

    public function setActress(?Actress $actress): static
    {
        $this->actress = $actress;

        return $this;
    }

    public function getVideoType(): ?VideoTypeEnum
    {
        return $this->videoType;
    }

    public function setVideoType(?VideoTypeEnum $videoType): static
    {
        $this->videoType = $videoType;

        return $this;
    }

    public function getWebsite(): ?string
    {
        return $this->website;
    }

    public function setWebsite(?string $website): static
    {
        $this->website = $website;

        return $this;
    }
}
