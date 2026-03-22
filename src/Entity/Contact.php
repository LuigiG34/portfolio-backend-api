<?php

namespace App\Entity;

use App\Repository\ContactRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use App\State\ContactStateProcessor;

#[ORM\Entity(repositoryClass: ContactRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(security: "is_granted('ROLE_ADMIN')"),
        new Get(security: "is_granted('ROLE_ADMIN')"),
        new Post(processor: ContactStateProcessor::class),
    ],
    normalizationContext: ['groups' => ['contact:read']],
    denormalizationContext: ['groups' => ['contact:write']],
)]
class Contact
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['contact:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    #[Groups(['contact:read', 'contact:write'])]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Assert\Email]
    #[Groups(['contact:read', 'contact:write'])]
    private ?string $email = null;

    #[ORM\Column(type: 'text')]
    #[Assert\NotBlank]
    #[Groups(['contact:read', 'contact:write'])]
    private ?string $message = null;

    #[ORM\Column]
    #[Groups(['contact:read'])]
    private DateTimeImmutable $sentAt;

    #[ORM\Column(length: 10)]
    #[Groups(['contact:read'])]
    private string $status = 'pending';

    public function __construct()
    {
        $this->sentAt = new DateTimeImmutable();
    }

    public function getId(): ?int { return $this->id; }

    public function getName(): ?string { return $this->name; }

    public function setName(string $name): static
    {
        $this->name = $name;
        return $this;
    }

    public function getEmail(): ?string { return $this->email; }

    public function setEmail(string $email): static
    {
        $this->email = $email;
        return $this;
    }

    public function getMessage(): ?string { return $this->message; }

    public function setMessage(string $message): static
    {
        $this->message = $message;
        return $this;
    }

    public function getSentAt(): DateTimeImmutable { return $this->sentAt; }

    public function getStatus(): string { return $this->status; }

    public function setStatus(string $status): static
    {
        $this->status = $status;
        return $this;
    }
}