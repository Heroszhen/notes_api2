<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\ActressPhotoRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ActressPhotoRepository::class)]
class ActressPhoto extends AbstractEntity
{
    #[ORM\ManyToOne(inversedBy: 'images')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotBlank]
    private ?Actress $actress = null;

    #[ORM\Column]
    private bool $visible = true;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private MediaObject $photo;

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

    public function getPhoto(): MediaObject
    {
        return $this->photo;
    }

    public function setPhoto(MediaObject $photo): static
    {
        $this->photo = $photo;

        return $this;
    }
}
