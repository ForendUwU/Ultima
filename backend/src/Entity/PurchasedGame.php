<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Repository\PurchasedGameRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: PurchasedGameRepository::class)]
#[ApiResource(
    shortName: 'Purchased Game',
    operations: [
        new Get(uriTemplate: 'api/purchased-games/{id}'),
        new GetCollection(uriTemplate: 'api/purchased-games'),
        new Post(uriTemplate: 'api/purchased-games'),
        new Patch(uriTemplate: 'api/purchased-games/{id}'),
        new Delete(uriTemplate: 'api/purchased-games/{id}'),
    ],
    normalizationContext: [
        'groups' => ['purchasedGame:read']
    ],
)]
#[ApiFilter(SearchFilter::class, properties: ['user.login' => 'partial'])]
class PurchasedGame
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['user:read', 'purchasedGame:read', 'game:read'])]
    private ?int $id = null;

    #[ORM\Column(nullable: false)]
    #[Groups(['user:read', 'purchasedGame:read'])]
    private ?float $hoursOfPlaying;

    #[ORM\Column]
    #[Groups(['user:read', 'purchasedGame:read'])]
    private ?\DateTimeImmutable $boughtAt;

    #[ORM\ManyToOne(inversedBy: 'purchasedGames')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['purchasedGame:read'])]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'purchasedGames')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['user:read', 'purchasedGame:read', 'user:write'])]
    private ?Game $game = null;

    public function __construct()
    {
        $this->boughtAt = new \DateTimeImmutable();
        $this->hoursOfPlaying = 0;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getHoursOfPlaying(): ?float
    {
        return $this->hoursOfPlaying;
    }

    public function setHoursOfPlaying(?float $hoursOfPlaying): static
    {
        $this->hoursOfPlaying = $hoursOfPlaying;

        return $this;
    }

    public function getBoughtAt(): ?\DateTimeImmutable
    {
        return $this->boughtAt;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getGame(): ?Game
    {
        return $this->game;
    }

    public function setGame(?Game $game): static
    {
        $this->game = $game;

        return $this;
    }
}
