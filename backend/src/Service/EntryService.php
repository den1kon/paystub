<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Entry;
use App\Repository\EntryRepository;
use App\Repository\ProjectRepository;
use InvalidArgumentException;
use RuntimeException;
use DateTime;

final class EntryService
{
    private EntryRepository $entryRepository;
    private ProjectRepository $projectRepository;

    public function __construct(EntryRepository $entryRepository, ProjectRepository $projectRepository)
    {
        $this->entryRepository = $entryRepository;
        $this->projectRepository = $projectRepository;
    }

    private function validateEntryData(DateTime $startedAt, DateTime $endedAt): void
    {
        if ($startedAt >= $endedAt) {
            throw new InvalidArgumentException('Start time must be before end time.');
        }
    }

    private function validateProjectId(?int $projectId): void
    {
        if ($projectId === null) {
            return;
        }

        $project = $this->projectRepository->findById($projectId);

        if ($project === null) {
            throw new InvalidArgumentException('Project with ID ' . $projectId . ' not found.');
        }

        if ($project->isDeleted()) {
            throw new InvalidArgumentException('Cannot assign entry to a deleted project.');
        }
    }

    public function createEntry(DateTime $startedAt, DateTime $endedAt, ?int $projectId = null): Entry
    {
        $this->validateEntryData($startedAt, $endedAt);
        $this->validateProjectId($projectId);
        $entry = new Entry($startedAt, $endedAt, $projectId);
        return $this->entryRepository->create($entry);
    }

    public function getEntry(int $id): Entry
    {
        $entry = $this->entryRepository->findById($id);

        if ($entry === null) {
            throw new RuntimeException('Entry not found.');
        }

        return $entry;
    }

    public function getAllEntries(): array
    {
        return $this->entryRepository->findAll();
    }

    public function updateEntry(int $id, DateTime $startedAt, DateTime $endedAt, ?int $projectId = null): Entry
    {
        $entry = $this->getEntry($id);

        $this->validateEntryData($startedAt, $endedAt);
        $this->validateProjectId($projectId);

        $entry->setStartedAt($startedAt);
        $entry->setEndedAt($endedAt);
        $entry->setProjectId($projectId);

        return $this->entryRepository->update($entry);
    }

    public function deleteEntry(int $id): void
    {
        $entry = $this->getEntry($id);

        if ($entry->isDeleted()) {
            throw new InvalidArgumentException('Entry is already deleted');
        }

        $this->entryRepository->delete($id);
    }
}
