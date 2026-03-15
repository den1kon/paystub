<?php

// Entity/Company.php

declare(strict_types=1);

namespace App\Entity;

use DateTime;

final class Company
{
    private ?int $id = null;
    private DateTime $createdAt;
    private DateTime $updatedAt;
    private string $name;
    private ?string $alias = null;
    private bool $isDeleted = false;

    public function __construct(
        string $name,
        ?string $alias = null,
    ) {
        $this->name = $name;
        $this->alias = $alias;
    }

    // Getters/Setters

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getAlias(): ?string
    {
        return $this->alias;
    }

    public function getUpdatedAt(): DateTime
    {
        return $this->updatedAt;
    }

    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    public function isDeleted(): bool
    {
        return $this->isDeleted;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function setAlias(?string $alias): void
    {
        $this->alias = $alias;
    }

    public function setCreatedAt(DateTime $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function setUpdatedAt(DateTime $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    public function setIsDeleted(bool $isDeleted): void
    {
        $this->isDeleted = $isDeleted;
    }

    public function delete(): void
    {
        $this->isDeleted = true;
    }

    public function restore(): void
    {
        $this->isDeleted = false;
    }
}
