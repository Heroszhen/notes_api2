<?php

declare(strict_types=1);

namespace App\Entity;

use App\Enum\VideoTypeEnum;
use App\Repository\VideoRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: VideoRepository::class)]
class Video extends AbstractEntity
{
    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    private ?string $title = null;

    #[ORM\ManyToOne(inversedBy: 'videos')]
    #[Assert\NotBlank]
    private ?Actress $actress = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?MediaObject $photo = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank]
    private ?string $videoUrl = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $originalVideoUrl = null;

    #[ORM\Column]
    private bool $visible = true;

    #[ORM\Column(enumType: VideoTypeEnum::class)]
    #[Assert\NotBlank]
    private VideoTypeEnum $videoType = VideoTypeEnum::EXTERN;

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getActress(): ?Actress
    {
        return $this->actress;
    }

    public function setActress(?Actress $actress): static
    {
        $this->actress = $actress;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getPhoto(): ?MediaObject
    {
        return $this->photo;
    }

    public function setPhoto(?MediaObject $photo): static
    {
        $this->photo = $photo;

        return $this;
    }

    public function getVideoUrl(): ?string
    {
        return $this->videoUrl;
    }

    public function setVideoUrl(string $videoUrl): static
    {
        $this->videoUrl = $videoUrl;

        return $this;
    }

    public function getOriginalVideoUrl(): ?string
    {
        return $this->originalVideoUrl;
    }

    public function setOriginalVideoUrl(?string $originalVideoUrl): static
    {
        $this->originalVideoUrl = $originalVideoUrl;

        return $this;
    }

    public function getVisible(): bool
    {
        return $this->visible;
    }

    public function setVisible(bool $visible): static
    {
        $this->visible = $visible;

        return $this;
    }

    public function getVideoType(): VideoTypeEnum
    {
        return $this->videoType;
    }

    public function setVideoType(VideoTypeEnum $videoType): static
    {
        $this->videoType = $videoType;

        return $this;
    }
}
