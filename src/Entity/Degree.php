<?php

namespace App\Entity;

use App\Repository\DegreeRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Delete;

#[ORM\Entity(repositoryClass: DegreeRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(security: "is_granted('ROLE_ADMIN')"),
        new Get(security: "is_granted('ROLE_ADMIN')"),
        new Post(security: "is_granted('ROLE_ADMIN')"),
        new Put(security: "is_granted('ROLE_ADMIN')"),
        new Delete(security: "is_granted('ROLE_ADMIN')"),
    ],
    normalizationContext: ['groups' => ['degree:read']],
    denormalizationContext: ['groups' => ['degree:write']],
)]
class Degree
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['degree:read'])]
    private ?int $id = null;

    #[ORM\Column(type: 'date')]
    #[Assert\NotNull]
    #[Groups(['degree:read', 'degree:write'])]
    private ?\DateTimeInterface $graduatedAt = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    #[Groups(['degree:read', 'degree:write'])]
    private ?string $title = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    #[Groups(['degree:read', 'degree:write'])]
    private ?string $schoolName = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['degree:read', 'degree:write'])]
    private ?Image $image = null;

    public function getId(): ?int { return $this->id; }

    public function getGraduatedAt(): ?\DateTimeInterface { return $this->graduatedAt; }

    public function setGraduatedAt(\DateTimeInterface $graduatedAt): static
    {
        $this->graduatedAt = $graduatedAt;
        return $this;
    }

    public function getTitle(): ?string { return $this->title; }

    public function setTitle(string $title): static
    {
        $this->title = $title;
        return $this;
    }

    public function getSchoolName(): ?string { return $this->schoolName; }

    public function setSchoolName(string $schoolName): static
    {
        $this->schoolName = $schoolName;
        return $this;
    }

    public function getImage(): ?Image { return $this->image; }

    public function setImage(Image $image): static
    {
        $this->image = $image;
        return $this;
    }
}
