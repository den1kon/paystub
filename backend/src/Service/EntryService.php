<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Entry;
use App\Repository\EntryRepository;
use App\Repository\ProjectRepository;
use InvalidArgumentException;
use RuntimeException;
use DateTime;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class EntryService
{
    private EntryRepository $entryRepository;
    private ProjectRepository $projectRepository;
    private ValidatorInterface $validator;

    public function __construct(EntryRepository $entryRepository, ProjectRepository $projectRepository, ValidatorInterface $validator)
    {
        $this->entryRepository = $entryRepository;
        $this->projectRepository = $projectRepository;
        $this->validator = $validator;
    }

    private function validateEntryInstance(Entry $entry): void
    {
        $errors = $this->validator->validate($entry);

        if (count($errors) > 0) {
            $messages = [];

            foreach ($errors as $error) {
                $messages[$error->getPropertyPath()] = $error->getMessage();
            }

            throw new InvalidArgumentException(json_encode($messages));
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
        $this->validateProjectId($projectId);
        $entry = new Entry($startedAt, $endedAt, $projectId);
        $this->validateEntryInstance($entry);
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

        $this->validateProjectId($projectId);

        $entry->setStartedAt($startedAt);
        $entry->setEndedAt($endedAt);
        $entry->setProjectId($projectId);

        $this->validateEntryInstance($entry);

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
