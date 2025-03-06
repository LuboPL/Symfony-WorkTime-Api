<?php
declare(strict_types=1);

namespace App\Service\WorkTimeCalculator;

use App\Entity\Employee\Employee;
use App\Enum\WorkTimeRules;
use App\Model\DailySummary;
use App\Model\MonthlySummary;
use App\Repository\WorkTime\WorkTimeRepository;

readonly class WorkTimeCalculator
{
    public function __construct(private WorkTimeRepository $workTimeRepository)
    {
    }

    public function calculatePerDay(Employee $employee, \DateTimeImmutable $dateTime): ?DailySummary
    {
        $workTime = $this->workTimeRepository->findOneByEmployeeAndDate($employee, $dateTime);

        if (null === $workTime) {
            return null;
        }

        $total = $this->workTimeRepository->getTotalHoursByEmployeeAndMonth(
            $employee,
            (int)$dateTime->format('m'),
            (int)$dateTime->format('Y')
        );
        $dailySummary = new DailySummary($workTime->totalHours);
        if ($total < WorkTimeRules::MONTHLY_NORM->value) {
            $dailySummary->calculatePayout((float)WorkTimeRules::DEFAULT_RATE->value, 0.0);

            return $dailySummary;
        }

        $dailySummary->calculatePayout(
            (float)WorkTimeRules::DEFAULT_RATE->value,
            (float)WorkTimeRules::RATE_MULTIPLIER->value
        );

        return $dailySummary;
    }

    public function calculatePerMonth(): MonthlySummary
    {
//        return new MonthlySummary(
//
//        );
    }

}