<?php

declare(strict_types=1);

namespace Masilia\ConsentBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Masilia\ConsentBundle\Repository\CookiePolicyRepository;

#[ORM\Entity(repositoryClass: CookiePolicyRepository::class)]
#[ORM\Table(name: 'masilia_cookie_policy')]
#[ORM\HasLifecycleCallbacks]
class CookiePolicy
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 20)]
    private string $version;

    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface $lastUpdated;

    #[ORM\Column(type: 'integer')]
    private int $expirationDays;

    #[ORM\Column(type: 'string', length: 50)]
    private string $cookiePrefix;

    #[ORM\Column(type: 'boolean')]
    private bool $isActive = false;

    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    private ?string $siteAccess = null;

    // Storage Configuration
    #[ORM\Column(type: 'string', length: 100)]
    private string $cookieName = 'imal_consent';

    #[ORM\Column(type: 'integer')]
    private int $cookieLifetime = 365;

    #[ORM\Column(type: 'string', length: 255)]
    private string $cookiePath = '/';

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $cookieDomain = null;

    #[ORM\Column(type: 'boolean')]
    private bool $cookieSecure = true;

    #[ORM\Column(type: 'boolean')]
    private bool $cookieHttpOnly = true;

    #[ORM\Column(type: 'string', length: 20)]
    private string $cookieSameSite = 'lax';

    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface $createdAt;

    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface $updatedAt;

    #[ORM\OneToMany(targetEntity: CookieCategory::class, mappedBy: 'policy', cascade: ['persist', 'remove'], orphanRemoval: true)]
    #[ORM\OrderBy(['position' => 'ASC'])]
    private Collection $categories;

    #[ORM\OneToMany(targetEntity: ThirdPartyService::class, mappedBy: 'policy', cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $thirdPartyServices;

    public function __construct()
    {
        $this->categories = new ArrayCollection();
        $this->thirdPartyServices = new ArrayCollection();
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
        $this->lastUpdated = new \DateTime();
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

    public function getVersion(): string
    {
        return $this->version;
    }

    public function setVersion(string $version): self
    {
        $this->version = $version;
        return $this;
    }

    public function getLastUpdated(): \DateTimeInterface
    {
        return $this->lastUpdated;
    }

    public function setLastUpdated(\DateTimeInterface $lastUpdated): self
    {
        $this->lastUpdated = $lastUpdated;
        return $this;
    }

    public function getExpirationDays(): int
    {
        return $this->expirationDays;
    }

    public function setExpirationDays(int $expirationDays): self
    {
        $this->expirationDays = $expirationDays;
        return $this;
    }

    public function getCookiePrefix(): string
    {
        return $this->cookiePrefix;
    }

    public function setCookiePrefix(string $cookiePrefix): self
    {
        $this->cookiePrefix = $cookiePrefix;
        return $this;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): self
    {
        $this->isActive = $isActive;
        return $this;
    }

    public function getSiteAccess(): ?string
    {
        return $this->siteAccess;
    }

    public function setSiteAccess(?string $siteAccess): self
    {
        $this->siteAccess = $siteAccess;
        return $this;
    }

    public function getCookieName(): string
    {
        return $this->cookieName;
    }

    public function setCookieName(string $cookieName): self
    {
        $this->cookieName = $cookieName;
        return $this;
    }

    public function getCookieLifetime(): int
    {
        return $this->cookieLifetime;
    }

    public function setCookieLifetime(int $cookieLifetime): self
    {
        $this->cookieLifetime = $cookieLifetime;
        return $this;
    }

    public function getCookiePath(): string
    {
        return $this->cookiePath;
    }

    public function setCookiePath(string $cookiePath): self
    {
        $this->cookiePath = $cookiePath;
        return $this;
    }

    public function getCookieDomain(): ?string
    {
        return $this->cookieDomain;
    }

    public function setCookieDomain(?string $cookieDomain): self
    {
        $this->cookieDomain = $cookieDomain;
        return $this;
    }

    public function isCookieSecure(): bool
    {
        return $this->cookieSecure;
    }

    public function setCookieSecure(bool $cookieSecure): self
    {
        $this->cookieSecure = $cookieSecure;
        return $this;
    }

    public function isCookieHttpOnly(): bool
    {
        return $this->cookieHttpOnly;
    }

    public function setCookieHttpOnly(bool $cookieHttpOnly): self
    {
        $this->cookieHttpOnly = $cookieHttpOnly;
        return $this;
    }

    public function getCookieSameSite(): string
    {
        return $this->cookieSameSite;
    }

    public function setCookieSameSite(string $cookieSameSite): self
    {
        $this->cookieSameSite = $cookieSameSite;
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

    /**
     * @return Collection<int, CookieCategory>
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function addCategory(CookieCategory $category): self
    {
        if (!$this->categories->contains($category)) {
            $this->categories->add($category);
            $category->setPolicy($this);
        }

        return $this;
    }

    public function removeCategory(CookieCategory $category): self
    {
        if ($this->categories->removeElement($category)) {
            if ($category->getPolicy() === $this) {
                $category->setPolicy(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, ThirdPartyService>
     */
    public function getThirdPartyServices(): Collection
    {
        return $this->thirdPartyServices;
    }

    public function addThirdPartyService(ThirdPartyService $service): self
    {
        if (!$this->thirdPartyServices->contains($service)) {
            $this->thirdPartyServices->add($service);
            $service->setPolicy($this);
        }

        return $this;
    }

    public function removeThirdPartyService(ThirdPartyService $service): self
    {
        if ($this->thirdPartyServices->removeElement($service)) {
            if ($service->getPolicy() === $this) {
                $service->setPolicy(null);
            }
        }

        return $this;
    }
}
