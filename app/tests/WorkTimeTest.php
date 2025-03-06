<?php
declare(strict_types=1);

namespace App\Tests;

use App\Entity\Employee\Employee;
use App\Entity\WorkTime\WorkTime;
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

    private function createWorkTimeWithInterval(
        string $startTime,
        string $endTime
    ): WorkTime {
        $baseDate = '2025-03-06';
        return new WorkTime(
            $this->employee,
            new \DateTime($baseDate),
            new \DateTimeImmutable( sprintf('%s %s', $baseDate, $startTime)),
            new \DateTimeImmutable(sprintf('%s %s', $baseDate, $endTime))
        );
    }
}