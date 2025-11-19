<?php

declare(strict_types=1);

namespace Masilia\ConsentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Masilia\ConsentBundle\Repository\ThirdPartyServiceRepository;

#[ORM\Entity(repositoryClass: ThirdPartyServiceRepository::class)]
#[ORM\Table(name: 'masilia_third_party_service')]
#[ORM\UniqueConstraint(name: 'unique_policy_identifier', columns: ['policy_id', 'identifier'])]
#[ORM\HasLifecycleCallbacks]
class ThirdPartyService
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: CookiePolicy::class, inversedBy: 'thirdPartyServices')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?CookiePolicy $policy = null;

    #[ORM\Column(type: 'string', length: 50)]
    private string $identifier;

    #[ORM\Column(type: 'string', length: 100)]
    private string $name;

    #[ORM\Column(type: 'string', length: 50)]
    private string $category;

    #[ORM\Column(type: 'text')]
    private string $description;

    #[ORM\Column(type: 'string', length: 500)]
    private string $privacyPolicyUrl;

    #[ORM\Column(type: 'string', length: 100)]
    private string $configKey;

    #[ORM\Column(type: 'string', length: 255)]
    private string $configValue;

    #[ORM\Column(type: 'string', length: 50, nullable: true)]
    private ?string $presetType = null;

    #[ORM\Column(type: 'boolean')]
    private bool $enabled = true;

    #[ORM\OneToOne(inversedBy: 'thirdPartyService', targetEntity: CookieCategory::class, cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: true, onDelete: 'CASCADE')]
    private ?CookieCategory $cookieCategory = null;

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
    public function preUpdate(): void
    {
        $this->updatedAt = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPolicy(): ?CookiePolicy
    {
        return $this->policy;
    }

    public function setPolicy(?CookiePolicy $policy): self
    {
        $this->policy = $policy;
        return $this;
    }

    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    public function setIdentifier(string $identifier): self
    {
        $this->identifier = $identifier;
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

    public function getCategory(): string
    {
        return $this->category;
    }

    public function setCategory(string $category): self
    {
        $this->category = $category;
        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function getPrivacyPolicyUrl(): string
    {
        return $this->privacyPolicyUrl;
    }

    public function setPrivacyPolicyUrl(string $privacyPolicyUrl): self
    {
        $this->privacyPolicyUrl = $privacyPolicyUrl;
        return $this;
    }

    public function getConfigKey(): string
    {
        return $this->configKey;
    }

    public function setConfigKey(string $configKey): self
    {
        $this->configKey = $configKey;
        return $this;
    }

    public function getConfigValue(): string
    {
        return $this->configValue;
    }

    public function setConfigValue(string $configValue): self
    {
        $this->configValue = $configValue;
        return $this;
    }

    public function getPresetType(): ?string
    {
        return $this->presetType;
    }

    public function setPresetType(?string $presetType): self
    {
        $this->presetType = $presetType;
        return $this;
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function setEnabled(bool $enabled): self
    {
        $this->enabled = $enabled;
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

    public function getCookieCategory(): ?CookieCategory
    {
        return $this->cookieCategory;
    }

    public function setCookieCategory(?CookieCategory $cookieCategory): self
    {
        $this->cookieCategory = $cookieCategory;
        return $this;
    }
}
