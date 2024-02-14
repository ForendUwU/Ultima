<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: 'user')]
#[ApiResource(
    operations: [
        new Get(uriTemplate: 'api/user/{id}'),
        new GetCollection(uriTemplate: 'api/user'),
        new Post(uriTemplate: 'api/user'),
        new Patch(uriTemplate: 'api/user/{id}'),
        new Delete(uriTemplate: 'api/user/{id}'),
    ],
    normalizationContext: [
        'groups' => ['user:read']
    ],
    denormalizationContext: [
        'groups' => ['user:write']
    ]
)]
#[UniqueEntity(fields: ['login'], message: 'User with this login already exists')]
#[UniqueEntity(fields: ['email'], message: 'User with this email already exists')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['user:read', 'purchasedGame:read'])]
    private ?int $id = 0;

    #[ORM\Column(length: 180, unique: true)]
    #[Groups(['user:read', 'user:write'])]
    #[Assert\NotBlank]
    #[Assert\Length(min: 5, max: 20, minMessage: 'Min size for login is 5', maxMessage: 'Max size for login is 20')]
    private ?string $login = null;

    #[ORM\Column]
    private array $roles = [];

    #[ORM\Column]
    #[Groups(['user:read', 'user:write'])]
    #[Assert\NotBlank]
    private ?string $password = null;

    #[ORM\Column(length: 255)]
    #[Groups(['user:read', 'user:write'])]
    #[Assert\NotBlank]
    #[Assert\Length(
        min: 2,
        max: 20,
        minMessage: 'Min size for nickname is 2',
        maxMessage: 'Max size for nickname is 20'
    )]
    private ?string $nickname = null;

    #[ORM\Column]
    #[Groups('user:read')]
    private ?float $balance;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['user:read', 'user:write'])]
    #[Assert\Length(
        min: 2,
        max: 20,
        minMessage: 'Min size for first name is 2',
        maxMessage: 'Max size for first name is 20'
    )]
    private ?string $firstName = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['user:read', 'user:write'])]
    #[Assert\Length(
        min: 2,
        max: 20,
        minMessage: 'Min size for last name is 2',
        maxMessage: 'Max size for last name is 20'
    )]
    private ?string $lastName = null;

    #[ORM\Column]
    #[Groups('user:read')]
    private ?\DateTimeImmutable $createdAt;

    #[ORM\Column(length: 255)]
    #[Groups(['user:read', 'user:write'])]
    #[Assert\NotBlank]
    #[Assert\Email]
    private ?string $email = null;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: PurchasedGame::class, cascade: ['persist'])]
    #[Groups(['user:read', 'user:write'])]
    private Collection $purchasedGames;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['user:read'])]
    private ?string $token = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->balance = 0;
        $this->purchasedGames = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLogin(): ?string
    {
        return $this->login;
    }

    public function setLogin(string $login): static
    {
        $this->login = $login;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->login;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        $this->token = null;
    }

    public function getNickname(): ?string
    {
        return $this->nickname;
    }

    public function setNickname(string $nickname): static
    {
        $this->nickname = $nickname;

        return $this;
    }

    public function getBalance(): ?float
    {
        return $this->balance;
    }

    public function setBalance(float $balance): static
    {
        $this->balance = $balance;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(?string $firstName): static
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(?string $lastName): static
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
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
            $purchasedGame->setUser($this);
        }

        return $this;
    }

    public function removePurchasedGame(PurchasedGame $purchasedGame): static
    {
        if (
            $this->purchasedGames->removeElement($purchasedGame)
            && $purchasedGame->getUser() === $this
        ) {
            $purchasedGame->setUser(null);
        }

        return $this;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(?string $token): static
    {
        $this->token = $token;

        return $this;
    }
}
