<?php

declare(strict_types=1);

namespace App\Entity;

use App\Enum\VideoTypeEnum;
use App\Repository\VideoRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

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
    private ?string $videoUrl = null;

    #[ORM\Column]
    private bool $visible = true;

    #[ORM\Column(enumType: VideoTypeEnum::class)]
    #[Assert\NotBlank]
    private VideoTypeEnum $videoType = VideoTypeEnum::EXTERN;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank]
    #[Assert\Url(protocols: ['https'], requireTld: true)]
    private ?string $website = null;

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

    public function getWebsite(): ?string
    {
        return $this->website;
    }

    public function setWebsite(?string $website): static
    {
        $this->website = $website;

        return $this;
    }

    #[Assert\Callback]
    public function validate(ExecutionContextInterface $context): void
    {
        if (empty($this->videoUrl) && empty($this->website)) {
            $context->buildViolation('Veuillez uploader une video')
                ->atPath('videoUrl')
                ->addViolation();
        }
    }
}
