<?php

declare(strict_types=1);

namespace App\Entity;

use DateTime;

final class Entry
{
    private ?int $id = null;
    private ?int $projectId = null;
    private DateTime $createdAt;
    private DateTime $updatedAt;
    private DateTime $startedAt;
    private DateTime $endedAt;
    private bool $isDeleted = false;

    public function __construct(
        DateTime $startedAt,
        DateTime $endedAt,
        ?int $projectId = null,
    ) {
        $this->startedAt = $startedAt;
        $this->endedAt = $endedAt;
        $this->projectId = $projectId;
    }

    // Getters/Setters

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProjectId(): ?int
    {
        return $this->projectId;
    }

    public function getStartedAt(): DateTime
    {
        return $this->startedAt;
    }

    public function getEndedAt(): DateTime
    {
        return $this->endedAt;
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

    public function setStartedAt(DateTime $startedAt): void
    {
        $this->startedAt = $startedAt;
    }

    public function setEndedAt(DateTime $endedAt): void
    {
        $this->endedAt = $endedAt;
    }

    public function setProjectId(?int $projectId): void
    {
        $this->projectId = $projectId;
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
