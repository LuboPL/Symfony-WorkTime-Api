<?php
declare(strict_types=1);

namespace App\Service\WorkTimeCalculator;

use App\Entity\Employee\Employee;
use App\Model\DailySummary;
use App\Model\MonthlySummary;
use App\Model\SummaryInterface;
use App\Repository\WorkTime\WorkTimeRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;

readonly class WorkTimeCalculator
{
    public function __construct(private WorkTimeRepository $workTimeRepository)
    {
    }

    /**
     * @throws \DateMalformedStringException
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function calculatePerDay(Employee $employee, \DateTimeImmutable $dateTime): SummaryInterface
    {
        $workTime = $this->workTimeRepository->findOneByEmployeeAndDate($employee, $dateTime);
        $total = $this->workTimeRepository->getTotalHoursByEmployeeAndMonthUntilDay($employee, $dateTime);

        $dailySummary = new DailySummary($workTime->totalHours, $total);
        $dailySummary->calculatePayout();

        return $dailySummary;
    }

    /**
     * @throws \DateMalformedStringException
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function calculatePerMonth(Employee $employee, \DateTimeImmutable $dateTime): SummaryInterface
    {
        $total = $this->workTimeRepository->getTotalHoursByEmployeeAndMonth($employee, $dateTime);
        $monthlySummary = new MonthlySummary($total);
        $monthlySummary->calculatePayout();

        return $monthlySummary;
    }
}