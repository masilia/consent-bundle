<?php

declare(strict_types=1);

namespace Masilia\ConsentBundle\ValueObject;

class ConsentPreferences
{
    private array $categories;
    private string $version;
    private \DateTimeInterface $timestamp;

    public function __construct(array $categories, string $version, ?\DateTimeInterface $timestamp = null)
    {
        $this->categories = $categories;
        $this->version = $version;
        $this->timestamp = $timestamp ?? new \DateTime();
    }

    public function getCategories(): array
    {
        return $this->categories;
    }

    public function hasConsent(string $category): bool
    {
        return isset($this->categories[$category]) && $this->categories[$category] === true;
    }

    public function getVersion(): string
    {
        return $this->version;
    }

    public function getTimestamp(): \DateTimeInterface
    {
        return $this->timestamp;
    }

    public function toArray(): array
    {
        return [
            'categories' => $this->categories,
            'version' => $this->version,
            'timestamp' => $this->timestamp->format('c'),
        ];
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['categories'] ?? [],
            $data['version'] ?? '1.0.0',
            isset($data['timestamp']) ? new \DateTime($data['timestamp']) : null
        );
    }
}
