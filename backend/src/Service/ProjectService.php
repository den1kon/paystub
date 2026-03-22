<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Project;
use App\Repository\ProjectRepository;
use App\Repository\CompanyRepository;
use InvalidArgumentException;
use RuntimeException;

final class ProjectService
{
    private ProjectRepository $projectRepository;
    private CompanyRepository $companyRepository;

    public function __construct(ProjectRepository $projectRepository, CompanyRepository $companyRepository)
    {
        $this->projectRepository = $projectRepository;
        $this->companyRepository = $companyRepository;
    }

    private function validateProjectData(string $name, ?string $alias = null): void
    {
        if (empty(trim($name))) {
            throw new InvalidArgumentException('Project name cannot be empty.');
        }

        if (strlen($name) > 60) {
            throw new InvalidArgumentException('Project name must not exceed 60 characters.');
        }

        if ($alias !== null && strlen($alias) > 40) {
            throw new InvalidArgumentException('Project alias must not exceed 40 characters.');
        }
    }

    private function validateCompanyId(?int $companyId): void
    {
        if ($companyId === null) {
            return;
        }

        $company = $this->companyRepository->findById($companyId);

        if ($company === null) {
            throw new InvalidArgumentException('Company with ID ' . $companyId . ' not found.');
        }

        if ($company->isDeleted()) {
            throw new InvalidArgumentException('Cannot assign project to a deleted company.');
        }
    }

    public function createProject(string $name, ?int $companyId = null, ?string $alias = null): Project
    {
        $this->validateProjectData($name, $alias);
        $this->validateCompanyId($companyId);
        $project = new Project($name, $companyId, $alias);
        return $this->projectRepository->create($project);
    }

    public function getProject(int $id): Project
    {
        $project = $this->projectRepository->findById($id);

        if ($project === null) {
            throw new RuntimeException('Project not found.');
        }

        return $project;
    }

    public function getAllProjects(): array
    {
        return $this->projectRepository->findAll();
    }

    public function updateProject(int $id, string $name, ?int $companyId = null, ?string $alias = null): Project
    {
        $project = $this->getProject($id);

        $this->validateProjectData($name, $alias);
        $this->validateCompanyId($companyId);

        $project->setName($name);
        $project->setAlias($alias);
        $project->setCompanyId($companyId);

        return $this->projectRepository->update($project);
    }

    public function deleteProject(int $id): void
    {
        $project = $this->getProject($id);

        if ($project->isDeleted()) {
            throw new InvalidArgumentException('Project is already deleted');
        }

        $this->projectRepository->delete($id);
    }
}
