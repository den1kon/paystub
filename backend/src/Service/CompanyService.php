<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Company;
use App\Repository\CompanyRepository;
use InvalidArgumentException;
use RuntimeException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class CompanyService
{
    private CompanyRepository $companyRepository;
    private ValidatorInterface $validator;

    public function __construct(CompanyRepository $companyRepository, ValidatorInterface $validator)
    {
        $this->companyRepository = $companyRepository;
        $this->validator = $validator;
    }

    private function validateCompanyInstance(Company $company): void
    {
        $errors = $this->validator->validate($company);

        if (count($errors) > 0) {
            $messages = [];

            foreach ($errors as $error) {
                $messages[$error->getPropertyPath()] = $error->getMessage();
            }

            throw new InvalidArgumentException(json_encode($messages));
        }
    }

    public function createCompany(string $name, ?string $alias = null): Company
    {
        $company = new Company($name, $alias);
        $this->validateCompanyInstance($company);

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

        $company->setName($name);
        $company->setAlias($alias);

        $this->validateCompanyInstance($company);


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
