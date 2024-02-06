<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Odm\Filter\RangeFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Repository\GamesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: GamesRepository::class)]
#[ApiResource(
    shortName: 'Game',
    operations: [
        new Get(
            uriTemplate: 'api/games/{id}',
            security: 'is_granted(["ROLE_USER"])'
        ),
        new GetCollection(
            uriTemplate: 'api/games',
        ),
        new Post(uriTemplate: 'api/games'),
        new Patch(uriTemplate: 'api/games/{id}'),
        new Delete(uriTemplate: 'api/games/{id}',),
    ],
    normalizationContext: [
        'groups' => ['game:read']
    ],
)]
#[UniqueEntity(fields: ['title'], message: 'Game with this title already exists')]
class Game
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['game:read', 'purchasedGame:read', 'user:read'])]
    private ?int $id = 0;

    #[ORM\Column(length: 255)]
    #[ApiFilter(SearchFilter::class, strategy: 'partial')]
    #[Assert\NotBlank]
    #[Assert\Length(min: 2, max: 50, minMessage: 'Min size for title is 2', maxMessage: 'Max size for title is 50')]
    #[Groups(['game:read', 'purchasedGame:read', 'user:read'])]
    private ?string $title = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Assert\Length(
        min: 10,
        max: 250,
        minMessage: 'Min size for description is 10',
        maxMessage: 'Max size for description is 250'
    )]
    #[Groups(['game:read'])]
    private ?string $description = null;

    #[ORM\Column]
    #[ApiFilter(RangeFilter::class)]
    #[Assert\GreaterThanOrEqual(0)]
    #[Groups(['game:read'])]
    private ?float $price = 0;

    #[ORM\Column]
    #[Groups(['game:read'])]
    private ?\DateTimeImmutable $publishedAt;

    #[ORM\OneToMany(mappedBy: 'game', targetEntity: PurchasedGame::class)]
    #[Groups(['game:read', 'game:write'])]
    private Collection $purchasedGames;

    public function __construct()
    {
        $this->publishedAt = new \DateTimeImmutable();
        $this->purchasedGames = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getPublishedAt(): ?\DateTimeImmutable
    {
        return $this->publishedAt;
    }

    /**
     * @return Collection<int, PurchasedGame>
     */
    public function getPurchasedGames(): Collection
    {
        return $this->purchasedGames;
    }

    public function addPurchasedGame(PurchasedGame $purchasedGame): static
    {
        if (!$this->purchasedGames->contains($purchasedGame)) {
            $this->purchasedGames->add($purchasedGame);
            $purchasedGame->setGame($this);
        }

        return $this;
    }

    public function removePurchasedGame(PurchasedGame $purchasedGame): static
    {
        if (
            $this->purchasedGames->removeElement($purchasedGame)
            && $purchasedGame->getGame() === $this
        ) {
            $purchasedGame->setGame(null);
        }

        return $this;
    }
}
