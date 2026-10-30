<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\ActressPhotoRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ActressPhotoRepository::class)]
class ActressPhoto extends AbstractEntity
{
    #[ORM\ManyToOne(inversedBy: 'images')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Actress $actress = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column]
    private bool $visible = true;

    public function getActress(): ?Actress
    {
        return $this->actress;
    }

    public function setActress(?Actress $actress): static
    {
        $this->actress = $actress;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function isVisible(): bool
    {
        return $this->visible;
    }

    public function setVisible(bool $visible): static
    {
        $this->visible = $visible;

        return $this;
    }
}
