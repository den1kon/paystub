<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Company;
use App\Repository\CompanyRepository;
use InvalidArgumentException;
use RuntimeException;

final class CompanyService
{
    private CompanyRepository $companyRepository;

    public function __construct(CompanyRepository $companyRepository)
    {
        $this->companyRepository = $companyRepository;
    }

    private function validateCompanyData(string $name, ?string $alias = null): void
    {
        if (empty(trim($name))) {
            throw new InvalidArgumentException('Company name cannot be empty.');
        }

        if (strlen($name) > 60) {
            throw new InvalidArgumentException('Company name must not exceed 60 characters.');
        }

        if ($alias !== null && strlen($alias) > 40) {
            throw new InvalidArgumentException('Company alias must not exceed 40 characters.');
        }
    }

    public function createCompany(string $name, ?string $alias = null): Company
    {
        $this->validateCompanyData($name, $alias);
        $company = new Company($name, $alias);
        return $this->companyRepository->create($company);
    }

    public function getCompany(int $id): Company
    {
        $company = $this->companyRepository->findById($id);

        if ($company === null) {
            throw new RuntimeException('Company not found.');
        }

        return $company;
    }

    public function getAllCompanies(): array
    {
        return $this->companyRepository->findAll();
    }

    public function updateCompany(int $id, string $name, ?string $alias = null): Company
    {
        $company = $this->getCompany($id);

        $this->validateCompanyData($name, $alias);

        $company->setName($name);
        $company->setAlias($alias);

        return $this->companyRepository->update($company);
    }

    public function deleteCompany(int $id): void
    {
        $company = $this->getCompany($id);

        if ($company->isDeleted()) {
            throw new InvalidArgumentException('Company is already deleted');
        }

        $this->companyRepository->delete($id);
    }
}
