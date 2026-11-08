<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\CategoryRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[UniqueEntity('name')]
#[ORM\Entity(repositoryClass: CategoryRepository::class)]
class Category extends AbstractEntity
{
    #[ORM\Column(length: 30, unique: true)]
    #[Assert\NotBlank]
    #[Assert\Length(
        min: 2,
        max: 30,
    )]
    private ?string $name = null;

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }
}
