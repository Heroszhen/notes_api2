<?php

declare(strict_types=1);

namespace App\Model;

use App\Entity\Actress;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

final class ActressPhotoModel
{
    #[Assert\NotNull]
    private ?Actress $actress = null;

    private bool $visible = true;

    /**
     * @var UploadedFile[]
     */
    #[Assert\Count(min: 1)]
    #[Assert\All([
        new Assert\Image(maxSize: '10M'),
    ])]
    private array $photos = [];

    public function getActress(): ?Actress
    {
        return $this->actress;
    }

    public function setActress(?Actress $actress): static
    {
        $this->actress = $actress;

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

    public function getPhotos(): array
    {
        return $this->photos;
    }

    public function setPhotos(array $photos): static
    {
        $this->photos = $photos;

        return $this;
    }
}
