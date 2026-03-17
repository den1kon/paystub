<?php

// Repository/CompanyRepository.php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Company;
use PDO;
use RuntimeException;
use PDOStatement;
use PDOException;
use DateTime;
use InvalidArgumentException;

final class CompanyRepository
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
            $this->createStmt = $this->pdo->prepare('INSERT INTO companies (name, alias) VALUES (:name, :alias);');
            $this->findByIdStmt = $this->pdo->prepare('SELECT * FROM companies WHERE id = :id;');
            $this->findAllStmt = $this->pdo->prepare('SELECT * FROM companies;');
            $this->updateStmt = $this->pdo->prepare('UPDATE companies SET name = :name, alias = :alias WHERE id = :id;');
            $this->deleteStmt = $this->pdo->prepare('UPDATE companies SET is_deleted = 1 WHERE id = :id;');
            $this->hardDeleteStmt = $this->pdo->prepare('DELETE FROM companies WHERE id = :id;');
        } catch (PDOException $e) {
            throw new RuntimeException('Failed to prepare company statements: ' . $e->getMessage(), 0, $e);
        }
    }

    private function hydrate(array $row): Company
    {
        if (!isset($row['id'], $row['created_at'], $row['updated_at'], $row['name'], $row['is_deleted'])) {
            throw new InvalidArgumentException('Missing required fields in companies row');
        }

        $company = new Company(
            (string) $row['name'],
            isset($row['alias']) && $row['alias'] !== null ? (string)$row['alias'] : null
        );

        $company->setId((int) $row['id']);
        $company->setCreatedAt(new DateTime((string) $row['created_at']));
        $company->setUpdatedAt(new DateTime((string) $row['updated_at']));
        $company->setIsDeleted((bool) $row['is_deleted']);

        return $company;
    }

    public function create(Company $company): Company
    {
        try {
            $result = $this->createStmt->execute([':name' => $company->getName(), ':alias' => $company->getAlias()]);

            if (!$result) {
                throw new RuntimeException('Failed to insert company: ' . ', ' . $this->createStmt->errorInfo());
            }

            $id = (int) $this->pdo->lastInsertId();

            $created = $this->findById($id);

            if ($created === null) {
                throw new RuntimeException('Failed to retrieve created company');
            }

            return $created;
        } catch (PDOException $e) {
            throw new RuntimeException('Database error during company creation: ' . $e->getMessage(), 0, $e);
        }
    }

    public function findById(int $id): ?Company
    {
        try {
            $result = $this->findByIdStmt->execute([':id' => $id]);

            if (!$result) {
                throw new RuntimeException('Failed to find company by id: ' . ', ' . $this->findByIdStmt->errorInfo());
            }

            $row = $this->findByIdStmt->fetch(PDO::FETCH_ASSOC);

            if ($row === false) {
                return null;
            }

            return $this->hydrate($row);
        } catch (PDOException $e) {
            throw new RuntimeException('Database error during finding company by id: ' . $e->getMessage(), 0, $e);
        }
    }

    public function findAll(): array
    {
        try {
            $result = $this->findAllStmt->execute();

            if (!$result) {
                throw new RuntimeException('Failed to find all companies: ' . ', ' . $this->findAllStmt->errorInfo());
            }

            $rows = $this->findAllStmt->fetchAll(PDO::FETCH_ASSOC);

            if ($rows === false) {
                return [];
            }

            return array_map(fn (array $row) => $this->hydrate($row), $rows);
        } catch (PDOException $e) {
            throw new RuntimeException('Database error during finding all companies: ' . $e->getMessage(), 0, $e);
        }
    }

    public function update(Company $company): Company
    {
        if ($company->getId() === null) {
            throw new InvalidArgumentException('Failed to update company with no ID.');
        }

        try {
            $result = $this->updateStmt->execute([
                ':name' => $company->getName(),
                ':alias' => $company->getAlias(),
                ':id' => $company->getId()
            ]);

            if (!$result) {
                throw new RuntimeException('Failed to update company: ' . ', ' . $this->updateStmt->errorInfo());
            }

            $updated = $this->findById($company->getId());

            if ($updated === null) {
                throw new RuntimeException('Failed to retrieve updated company.');
            }

            return $updated;
        } catch (PDOException $e) {
            throw new RuntimeException('Database error during updating company: ' . $e->getMessage(), 0, $e);
        }
    }

    public function delete(Company $company): Company
    {
        if ($company->getId() === null) {
            throw new InvalidArgumentException('Failed to soft-delete company with no ID.');
        }

        try {
            $result = $this->deleteStmt->execute([
                ':id' => $company->getId()
            ]);

            if (!$result) {
                throw new RuntimeException('Failed to soft-delete company: ' . ', ' . $this->deleteStmt->errorInfo());
            }

            $deleted = $this->findById($company->getId());

            if ($deleted === null) {
                throw new RuntimeException('Failed to retrieve soft-deleted company.');
            }

            return $deleted;
        } catch (PDOException $e) {
            throw new RuntimeException('Database error during soft-deleting company: ' . $e->getMessage(), 0, $e);
        }
    }

    public function hardDelete(int $id): void
    {
        try {
            $result = $this->hardDeleteStmt->execute([
                ':id' => $id
            ]);

            if (!$result) {
                throw new RuntimeException('Failed to hard-delete company: ' . ', ' . $this->hardDeleteStmt->errorInfo());
            }
        } catch (PDOException $e) {
            throw new RuntimeException('Database error during hard-deleting company: ' . $e->getMessage(), 0, $e);
        }
    }
}
