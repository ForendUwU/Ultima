<?php

namespace App\Entity;

use App\Repository\TokenRepository;
use Doctrine\ORM\Mapping as ORM;
use Firebase\JWT\JWT;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: TokenRepository::class)]
class Token
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'tokens')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $ownedBy = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $expiresAt;

    #[ORM\Column(nullable: false)]
    #[Groups(['user:read'])]
    private string $token;

    #[ORM\Column]
    private array $scopes = [];

    private const PRIVATE_KEY = 'WHFZQGXm#k$mBzX]A0f(=g^GbcFz5,~zUQY:$kGdEvu((%s*EmSRQFJ[/#qW^';

    private const ALGORITHM = 'HS256';

    public function __construct()
    {
        $expireTime = new \DateTimeImmutable();
        $this->expiresAt = $expireTime->modify('+1 day');
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOwnedBy(): ?User
    {
        return $this->ownedBy;
    }

    public function setOwnedBy(?User $ownedBy): static
    {
        $this->ownedBy = $ownedBy;

        $payload = [
            'id' => $ownedBy->getId(),
            'login' => $ownedBy->getLogin(),
            'email' => $ownedBy->getEmail(),
            //TODO change role .
            'role' => $ownedBy->getRoles()
        ];

        $this->token = JWT::encode($payload, self::PRIVATE_KEY, self::ALGORITHM);

        return $this;
    }

    public function getExpiresAt(): ?\DateTimeImmutable
    {
        return $this->expiresAt;
    }

    public function setExpiresAt(?\DateTimeImmutable $expiresAt): static
    {
        $this->expiresAt = $expiresAt;

        return $this;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(string $token): static
    {
        $this->token = $token;

        return $this;
    }

    public function getScopes(): array
    {
        return $this->scopes;
    }

    public function setScopes(array $scopes): static
    {
        $this->scopes = $scopes;

        return $this;
    }

    public function isValid(): bool
    {
        return $this->expiresAt === null || $this->expiresAt > new \DateTimeImmutable();
    }
}
