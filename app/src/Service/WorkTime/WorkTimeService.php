<?php
declare(strict_types=1);

namespace App\Service\WorkTime;

use App\Entity\Employee\Employee;
use App\Entity\WorkTime\WorkTime;
use App\Exception\WorkTimeException;
use App\Repository\Employee\EmployeeRepository;
use App\Repository\WorkTime\WorkTimeRepository;
use DateTimeImmutable;
use Doctrine\ORM\NonUniqueResultException;

readonly class WorkTimeService
{
    public function __construct(
        private WorkTimeRepository $workTimeRepository,
        private EmployeeRepository $employeeRepository,
    )
    {
    }

    /**
     * @throws WorkTimeException
     * @throws NonUniqueResultException
     */
    public function createWorkTime(array $data, \DateTime $today): WorkTime
    {
        $employee = $this->employeeRepository->findById($data['employeeUuid']);

        $startTime = DateTimeImmutable::createFromFormat('d.m.Y H:i', $data['startTime']);
        $endTime = DateTimeImmutable::createFromFormat('d.m.Y H:i', $data['endTime']);

        return new WorkTime(
            $employee,
            $today,
            $startTime,
            $endTime,
        );
    }

    public function saveWorkTime(WorkTime $workTime): void
    {
        $this->workTimeRepository->save($workTime);
    }

    /**
     * @throws NonUniqueResultException
     */
    public function getEmployee(string $employeeUuid): ?Employee
    {
        return $this->employeeRepository->findById($employeeUuid);
    }
}