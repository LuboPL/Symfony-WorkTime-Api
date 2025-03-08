<?php
declare(strict_types=1);

namespace App\Tests;

use App\Entity\Employee\Employee;
use App\Entity\WorkTime\WorkTime;
use App\Exception\WorkTimeException;
use App\Model\WorkTimeRules;
use PHPUnit\Framework\TestCase;

class WorkTimeTest extends TestCase
{
    private Employee $employee;

    protected function setUp(): void
    {
        $this->employee = $this->createMock(Employee::class);
    }

    public function testTotalHoursCalculation(): void
    {
        $cases = [
            ['09:00', '17:00', 8.0],
            ['09:00', '17:14', 8.0],
            ['09:00', '17:15', 8.5],
            ['09:00', '17:29', 8.5],
            ['09:00', '17:30', 8.5],
            ['09:00', '17:44', 8.5],
            ['09:00', '17:45', 9.0]
        ];

        foreach ($cases as [$start, $end, $expected]) {
            $workTime = $this->createWorkTimeWithInterval($start, $end);
            $this->assertEquals(
                $expected,
                $workTime->totalHours
            );
        }
    }

    public function testHoursExceedsLimit(): void
    {
        $this->expectException(WorkTimeException::class);
        $this->expectExceptionMessage(
            sprintf(
                'Work time cannot be bigger than limited hours: %d',
                WorkTimeRules::DAILY_HOURS_LIMIT
            )
        );
        new WorkTime(
            $this->employee,
            new \DateTime('2025-03-06'),
            new \DateTimeImmutable('2025-03-06 01:00'),
            new \DateTimeImmutable('2025-03-06 14:00')
        );
    }

    private function createWorkTimeWithInterval(
        string $startTime,
        string $endTime
    ): WorkTime {
        $baseDate = '2025-03-06';
        return new WorkTime(
            $this->employee,
            new \DateTime($baseDate),
            new \DateTimeImmutable(sprintf('%s %s', $baseDate, $startTime)),
            new \DateTimeImmutable(sprintf('%s %s', $baseDate, $endTime))
        );
    }
}