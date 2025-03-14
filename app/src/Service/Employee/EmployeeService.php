<?php
declare(strict_types=1);

namespace App\Service\Employee;

use App\Entity\Employee\Employee;
use App\Repository\Employee\EmployeeRepository;

readonly class EmployeeService
{
    public function __construct(private EmployeeRepository $employeeRepository)
    {
    }

    public function createEmployee(array $data): Employee
    {
        return new Employee($data['firstName'], $data['lastName']);
    }

    public function saveEmployee(Employee $employee): void
    {
        $this->employeeRepository->save($employee);
    }
}