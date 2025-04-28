<?php

namespace App\Entity;

use App\Entity\Users;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\ProfilsRepository;

#[ORM\HasLifecycleCallbacks] // Annotation Permet de dire que la classe a des méthodes de cycle de vie c'est à dire des méthodes qui seront appelées automatiquement par Doctrine à des moments précis du cycle de vie de l'entité (ex: avant la création, avant la mise à jour, etc.)
#[ORM\Entity(repositoryClass: ProfilsRepository::class)]
class Profils
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $picture = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $descriptif = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $dateBirth = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\OneToOne(inversedBy: 'profil', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Users $user = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable('now', new \DateTimeZone('Europe/Paris'));
    }

    #[ORM\PreUpdate] // Signifie que la méthode annoté sera appelée juste avant que l'entité soit mise à jour dans la base de données c'est à dire avant le flush (exécution de l'update)
    public function setUpdatedAtValue(): void
    {
        $this->updatedAt = new \DateTimeImmutable('now', new \DateTimeZone('Europe/Paris'));
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPicture(): ?string
    {
        return $this->picture;
    }

    public function setPicture(?string $picture): static
    {
        $this->picture = $picture;

        return $this;
    }

    public function getDescriptif(): ?string
    {
        return $this->descriptif;
    }

    public function setDescriptif(?string $descriptif): static
    {
        $this->descriptif = $descriptif;

        return $this;
    }

    public function getDateBirth(): ?\DateTimeImmutable
    {
        return $this->dateBirth;
    }

    public function setDateBirth(?\DateTimeImmutable $dateBirth): static
    {
        $this->dateBirth = $dateBirth;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getUser(): ?Users
    {
        return $this->user;
    }

    public function setUser(Users $user): static
    {
        $this->user = $user;

        return $this;
    }
}
