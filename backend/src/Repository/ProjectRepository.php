<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Project;
use PDO;
use RuntimeException;
use PDOStatement;
use PDOException;
use DateTime;
use InvalidArgumentException;

final class ProjectRepository
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
            $this->createStmt = $this->pdo->prepare('INSERT INTO projects (company_id, name, alias) VALUES (:company_id, :name, :alias);');
            $this->findByIdStmt = $this->pdo->prepare('SELECT * FROM projects WHERE id = :id;');
            $this->findAllStmt = $this->pdo->prepare('SELECT * FROM projects;');
            $this->updateStmt = $this->pdo->prepare('UPDATE projects SET company_id = :company_id, name = :name, alias = :alias WHERE id = :id;');
            $this->deleteStmt = $this->pdo->prepare('UPDATE projects SET is_deleted = 1 WHERE id = :id;');
            $this->hardDeleteStmt = $this->pdo->prepare('DELETE FROM projects WHERE id = :id;');
        } catch (PDOException $e) {
            throw new RuntimeException('Failed to prepare project statements: ' . $e->getMessage(), 0, $e);
        }
    }

    private function hydrate(array $row): Project
    {
        if (!isset($row['id'], $row['created_at'], $row['updated_at'], $row['name'], $row['is_deleted'])) {
            throw new InvalidArgumentException('Missing required fields in projects row');
        }

        $project = new Project(
            (string) $row['name'],
            isset($row['company_id']) && $row['company_id'] !== null ? (int)$row['company_id'] : null,
            isset($row['alias']) && $row['alias'] !== null ? (string)$row['alias'] : null
        );

        $project->setId((int) $row['id']);
        $project->setCreatedAt(new DateTime((string) $row['created_at']));
        $project->setUpdatedAt(new DateTime((string) $row['updated_at']));
        $project->setIsDeleted((bool) $row['is_deleted']);

        return $project;
    }

    public function create(Project $project): Project
    {
        try {
            $result = $this->createStmt->execute([
                ':company_id' => $project->getCompanyId(),
                ':name' => $project->getName(),
                ':alias' => $project->getAlias()
            ]);

            if (!$result) {
                throw new RuntimeException('Failed to insert project: ' . implode(', ', $this->createStmt->errorInfo()));
            }

            $id = (int) $this->pdo->lastInsertId();

            $created = $this->findById($id);

            if ($created === null) {
                throw new RuntimeException('Failed to retrieve created project');
            }

            return $created;
        } catch (PDOException $e) {
            throw new RuntimeException('Database error during project creation: ' . $e->getMessage(), 0, $e);
        }
    }

    public function findById(int $id): ?Project
    {
        try {
            $result = $this->findByIdStmt->execute([':id' => $id]);

            if (!$result) {
                throw new RuntimeException('Failed to find project by id: ' . implode(', ', $this->findByIdStmt->errorInfo()));
            }

            $row = $this->findByIdStmt->fetch(PDO::FETCH_ASSOC);

            if ($row === false) {
                return null;
            }

            return $this->hydrate($row);
        } catch (PDOException $e) {
            throw new RuntimeException('Database error during finding project by id: ' . $e->getMessage(), 0, $e);
        }
    }

    public function findAll(): array
    {
        try {
            $result = $this->findAllStmt->execute();

            if (!$result) {
                throw new RuntimeException('Failed to find all projects: ' . implode(', ', $this->findAllStmt->errorInfo()));
            }

            $rows = $this->findAllStmt->fetchAll(PDO::FETCH_ASSOC);

            if ($rows === false) {
                return [];
            }

            return array_map(fn (array $row) => $this->hydrate($row), $rows);
        } catch (PDOException $e) {
            throw new RuntimeException('Database error during finding all projects: ' . $e->getMessage(), 0, $e);
        }
    }

    public function update(Project $project): Project
    {
        if ($project->getId() === null) {
            throw new InvalidArgumentException('Failed to update project with no ID.');
        }

        try {
            $result = $this->updateStmt->execute([
                ':company_id' => $project->getCompanyId(),
                ':name' => $project->getName(),
                ':alias' => $project->getAlias(),
                ':id' => $project->getId()
            ]);

            if (!$result) {
                throw new RuntimeException('Failed to update project: ' . implode(', ', $this->updateStmt->errorInfo()));
            }

            $updated = $this->findById($project->getId());

            if ($updated === null) {
                throw new RuntimeException('Failed to retrieve updated project.');
            }

            return $updated;
        } catch (PDOException $e) {
            throw new RuntimeException('Database error during updating project: ' . $e->getMessage(), 0, $e);
        }
    }

    public function delete(int $id): Project
    {
        try {
            $result = $this->deleteStmt->execute([
                ':id' => $id
            ]);

            if (!$result) {
                throw new RuntimeException('Failed to soft-delete project: ' . implode(', ', $this->deleteStmt->errorInfo()));
            }

            $deleted = $this->findById($id);

            if ($deleted === null) {
                throw new RuntimeException('Failed to retrieve soft-deleted project.');
            }

            return $deleted;
        } catch (PDOException $e) {
            throw new RuntimeException('Database error during soft-deleting project: ' . $e->getMessage(), 0, $e);
        }
    }

    public function hardDelete(int $id): void
    {
        try {
            $result = $this->hardDeleteStmt->execute([
                ':id' => $id
            ]);

            if (!$result) {
                throw new RuntimeException('Failed to hard-delete project: ' . implode(', ', $this->hardDeleteStmt->errorInfo()));
            }
        } catch (PDOException $e) {
            throw new RuntimeException('Database error during hard-deleting project: ' . $e->getMessage(), 0, $e);
        }
    }
}
