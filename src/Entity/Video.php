<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\ExactFilter;
use ApiPlatform\Doctrine\Orm\Filter\PartialSearchFilter;
use ApiPlatform\Doctrine\Orm\Filter\SortFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\QueryParameter;
use App\Dto\VideoOutput;
use App\Enum\VideoTypeEnum;
use App\Provider\JasmineVideosProvider;
use App\Repository\VideoRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * ?search[title]=macau.
 */
#[ApiResource(
    order: ['createdAt' => 'DESC'],
    paginationItemsPerPage: 8,
    parameters: [
        'search[:property]' => new QueryParameter(
            properties: ['title', 'actress.name', 'website'],
            filter: new PartialSearchFilter(),
        ),
        'order[createdAt]' => new QueryParameter(
            property: 'createdAt',
            filter: new SortFilter(),
        ),
    ],
)]
#[GetCollection(
    uriTemplate: '/videos',
    name: 'videos_public',
    security: "is_granted('ROLE_USER')",
    normalizationContext: ['groups' => ['default', 'video:o']],
)]
#[GetCollection(
    uriTemplate: '/admin/videos',
    name: 'videos_admin',
    security: "is_granted('ROLE_ADMIN')",
    normalizationContext: ['groups' => ['default', 'video:o']],
    parameters: [
        'categories' => new QueryParameter(
            property: 'categories.name',
            filter: new ExactFilter(),
        ),
    ],
)]
#[GetCollection(
    uriTemplate: '/admin/videos/jasmine',
    name: 'videos_admin_jasmine',
    security: "is_granted('ROLE_ADMIN')",
    provider: JasmineVideosProvider::class,
    output: VideoOutput::class,
    normalizationContext: ['groups' => ['default', 'video:o']],
)]
#[ORM\Entity(repositoryClass: VideoRepository::class)]
class Video extends AbstractEntity
{
    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Groups(['video:o'])]
    private ?string $title = null;

    #[ORM\ManyToOne(inversedBy: 'videos')]
    #[Assert\NotBlank]
    #[Groups(['video:o'])]
    private ?Actress $actress = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['video:o'])]
    private ?string $description = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[Groups(['video:o'])]
    private ?MediaObject $photo = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['video:o'])]
    private ?string $videoUrl = null;

    #[ORM\Column]
    private bool $visible = true;

    #[ORM\Column(enumType: VideoTypeEnum::class)]
    #[Groups(['video:o'])]
    private VideoTypeEnum $videoType = VideoTypeEnum::EXTERN;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank]
    #[Assert\Url(protocols: ['https'], requireTld: true)]
    #[Groups(['video:o'])]
    private ?string $website = null;

    /**
     * @var Collection<int, Category>
     */
    #[ORM\ManyToMany(targetEntity: Category::class)]
    private Collection $categories;

    public function __construct()
    {
        $this->categories = new ArrayCollection();
    }

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

    /**
     * @return Collection<int, Category>
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function addCategory(Category $category): static
    {
        if (!$this->categories->contains($category)) {
            $this->categories->add($category);
        }

        return $this;
    }

    public function removeCategory(Category $category): static
    {
        $this->categories->removeElement($category);

        return $this;
    }
}
