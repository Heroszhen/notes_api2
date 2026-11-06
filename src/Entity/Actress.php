<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\ActressRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ActressRepository::class)]
#[UniqueEntity('name')]
class Actress extends AbstractEntity
{
    #[ORM\Column(length: 255, unique: true)]
    #[Assert\NotBlank]
    private ?string $name = null;

    #[ORM\Column(length: 50)]
    #[Assert\NotBlank]
    private ?string $country = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $birthday = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\ManyToOne]
    private ?MediaObject $photo = null;

    /**
     * @var Collection<int, ActressPhoto>
     */
    #[ORM\OneToMany(targetEntity: ActressPhoto::class, mappedBy: 'actress', orphanRemoval: true)]
    private Collection $images;

    /**
     * @var Collection<int, Video>
     */
    #[ORM\OneToMany(targetEntity: Video::class, mappedBy: 'actress')]
    private Collection $videos;

    public function __construct()
    {
        $this->images = new ArrayCollection();
        $this->videos = new ArrayCollection();
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

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(string $country): static
    {
        $this->country = $country;

        return $this;
    }

    public function getBirthday(): ?\DateTimeImmutable
    {
        return $this->birthday;
    }

    public function setBirthday(?\DateTimeImmutable $birthday): static
    {
        $this->birthday = $birthday;

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

    /**
     * @return Collection<int, ActressPhoto>
     */
    public function getImages(): Collection
    {
        return $this->images;
    }

    public function addImage(ActressPhoto $image): static
    {
        if (!$this->images->contains($image)) {
            $this->images->add($image);
            $image->setActress($this);
        }

        return $this;
    }

    public function removeImage(ActressPhoto $image): static
    {
        if ($this->images->removeElement($image)) {
            // set the owning side to null (unless already changed)
            if ($image->getActress() === $this) {
                $image->setActress(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Video>
     */
    public function getVideos(): Collection
    {
        return $this->videos;
    }

    public function addVideo(Video $video): static
    {
        if (!$this->videos->contains($video)) {
            $this->videos->add($video);
            $video->setActress($this);
        }

        return $this;
    }

    public function removeVideo(Video $video): static
    {
        if ($this->videos->removeElement($video)) {
            // set the owning side to null (unless already changed)
            if ($video->getActress() === $this) {
                $video->setActress(null);
            }
        }

        return $this;
    }
}
