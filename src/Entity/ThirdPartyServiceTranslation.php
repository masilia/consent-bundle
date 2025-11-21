<?php

declare(strict_types=1);

namespace Masilia\ConsentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Masilia\ConsentBundle\Repository\ThirdPartyServiceTranslationRepository;

#[ORM\Entity(repositoryClass: ThirdPartyServiceTranslationRepository::class)]
#[ORM\Table(name: 'masilia_third_party_service_translation')]
#[ORM\UniqueConstraint(name: 'service_language_unique', columns: ['service_id', 'language_code'])]
class ThirdPartyServiceTranslation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: ThirdPartyService::class, inversedBy: 'translations')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ThirdPartyService $service;

    #[ORM\Column(type: 'string', length: 10)]
    private string $languageCode;

    #[ORM\Column(type: 'string', length: 100)]
    private string $name;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface $createdAt;

    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface $updatedAt;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
    }

    #[ORM\PreUpdate]
    public function updateTimestamp(): void
    {
        $this->updatedAt = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getService(): ThirdPartyService
    {
        return $this->service;
    }

    public function setService(ThirdPartyService $service): self
    {
        $this->service = $service;
        return $this;
    }

    public function getLanguageCode(): string
    {
        return $this->languageCode;
    }

    public function setLanguageCode(string $languageCode): self
    {
        $this->languageCode = $languageCode;
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): \DateTimeInterface
    {
        return $this->updatedAt;
    }
}
