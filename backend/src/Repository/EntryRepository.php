<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Entry;
use PDO;
use RuntimeException;
use PDOStatement;
use PDOException;
use DateTime;
use InvalidArgumentException;

final class EntryRepository
{
    private PDO $pdo;
    private PDOStatement $createStmt;
    private PDOStatement $findByIdStmt;
    private PDOStatement $findAllStmt;
    private PDOStatement $updateStmt;
    private PDOStatement $deleteStmt;
    private PDOStatement $hardDeleteStmt;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
        $this->initializeStatements();
    }

    private function initializeStatements(): void
    {
        try {
            $this->createStmt = $this->pdo->prepare('INSERT INTO entries (project_id, started_at, ended_at) VALUES (:project_id, :started_at, :ended_at);');
            $this->findByIdStmt = $this->pdo->prepare('SELECT * FROM entries WHERE id = :id;');
            $this->findAllStmt = $this->pdo->prepare('SELECT * FROM entries;');
            $this->updateStmt = $this->pdo->prepare('UPDATE entries SET project_id = :project_id, started_at = :started_at, ended_at = :ended_at WHERE id = :id;');
            $this->deleteStmt = $this->pdo->prepare('UPDATE entries SET is_deleted = 1 WHERE id = :id;');
            $this->hardDeleteStmt = $this->pdo->prepare('DELETE FROM entries WHERE id = :id;');
        } catch (PDOException $e) {
            throw new RuntimeException('Failed to prepare entries statements: ' . $e->getMessage(), 0, $e);
        }
    }

    private function hydrate(array $row): Entry
    {
        if (!isset($row['id'], $row['created_at'], $row['updated_at'], $row['started_at'], $row['ended_at'], $row['is_deleted'])) {
            throw new InvalidArgumentException('Missing required fields in entry row');
        }

        $entry = new Entry(
            new DateTime((string) $row['started_at']),
            new DateTime((string) $row['ended_at']),
            isset($row['project_id']) && $row['project_id'] !== null ? (int)$row['project_id'] : null,
        );

        $entry->setId((int) $row['id']);
        $entry->setCreatedAt(new DateTime((string) $row['created_at']));
        $entry->setUpdatedAt(new DateTime((string) $row['updated_at']));
        $entry->setIsDeleted((bool) $row['is_deleted']);

        return $entry;
    }

    public function create(Entry $entry): Entry
    {
        try {
            $result = $this->createStmt->execute([
                ':project_id' => $entry->getProjectId(),
                ':started_at' => $entry->getStartedAt()->format('Y-m-d H:i:s'),
                ':ended_at' => $entry->getEndedAt()->format('Y-m-d H:i:s')
            ]);

            if (!$result) {
                throw new RuntimeException('Failed to insert entry: ' . implode(', ', $this->createStmt->errorInfo()));
            }

            $id = (int) $this->pdo->lastInsertId();

            $created = $this->findById($id);

            if ($created === null) {
                throw new RuntimeException('Failed to retrieve created entry');
            }

            return $created;
        } catch (PDOException $e) {
            throw new RuntimeException('Database error during entry creation: ' . $e->getMessage(), 0, $e);
        }
    }

    public function findById(int $id): ?Entry
    {
        try {
            $result = $this->findByIdStmt->execute([':id' => $id]);

            if (!$result) {
                throw new RuntimeException('Failed to find entry by id: ' . implode(', ', $this->findByIdStmt->errorInfo()));
            }

            $row = $this->findByIdStmt->fetch(PDO::FETCH_ASSOC);

            if ($row === false) {
                return null;
            }

            return $this->hydrate($row);
        } catch (PDOException $e) {
            throw new RuntimeException('Database error during finding entry by id: ' . $e->getMessage(), 0, $e);
        }
    }

    public function findAll(): array
    {
        try {
            $result = $this->findAllStmt->execute();

            if (!$result) {
                throw new RuntimeException('Failed to find all entries: ' . implode(', ', $this->findAllStmt->errorInfo()));
            }

            $rows = $this->findAllStmt->fetchAll(PDO::FETCH_ASSOC);

            if ($rows === false) {
                return [];
            }

            return array_map(fn (array $row) => $this->hydrate($row), $rows);
        } catch (PDOException $e) {
            throw new RuntimeException('Database error during finding all entries: ' . $e->getMessage(), 0, $e);
        }
    }

    public function update(Entry $entry): Entry
    {
        if ($entry->getId() === null) {
            throw new InvalidArgumentException('Failed to update entry with no ID.');
        }

        try {
            $result = $this->updateStmt->execute([
                ':project_id' => $entry->getProjectId(),
                ':started_at' => $entry->getStartedAt()->format('Y-m-d H:i:s'),
                ':ended_at' => $entry->getEndedAt()->format('Y-m-d H:i:s'),
                ':id' => $entry->getId()
            ]);

            if (!$result) {
                throw new RuntimeException('Failed to update entry: ' . implode(', ', $this->updateStmt->errorInfo()));
            }

            $updated = $this->findById($entry->getId());

            if ($updated === null) {
                throw new RuntimeException('Failed to retrieve updated entry.');
            }

            return $updated;
        } catch (PDOException $e) {
            throw new RuntimeException('Database error during updating entry: ' . $e->getMessage(), 0, $e);
        }
    }

    public function delete(int $id): Entry
    {
        try {
            $result = $this->deleteStmt->execute([
                ':id' => $id
            ]);

            if (!$result) {
                throw new RuntimeException('Failed to soft-delete entry: ' . implode(', ', $this->deleteStmt->errorInfo()));
            }

            $deleted = $this->findById($id);

            if ($deleted === null) {
                throw new RuntimeException('Failed to retrieve soft-deleted entry.');
            }

            return $deleted;
        } catch (PDOException $e) {
            throw new RuntimeException('Database error during soft-deleting entry: ' . $e->getMessage(), 0, $e);
        }
    }

    public function hardDelete(int $id): void
    {
        try {
            $result = $this->hardDeleteStmt->execute([
                ':id' => $id
            ]);

            if (!$result) {
                throw new RuntimeException('Failed to hard-delete entry: ' . implode(', ', $this->hardDeleteStmt->errorInfo()));
            }
        } catch (PDOException $e) {
            throw new RuntimeException('Database error during hard-deleting entry: ' . $e->getMessage(), 0, $e);
        }
    }
}
