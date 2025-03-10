<?php
declare(strict_types=1);

namespace App\Tests;

use App\Model\MonthlySummary;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class MonthlySummaryTest extends TestCase
{
    #[DataProvider('provideDataForRegularHours')]
    public function testCalculateFromRegularHours(
        float $totalHoursInMonth,
        float $expectedNormalHours,
        float $expectedOverTimeHours,
        float $expectedPayout
    ): void
    {
        $monthlySummary = new MonthlySummary($totalHoursInMonth);
        $monthlySummary->calculatePayout();

        $this->assertEquals($expectedPayout, $monthlySummary->getPayout());
        $this->assertEquals($expectedNormalHours, $monthlySummary->getNormalHours());
        $this->assertEquals($expectedOverTimeHours, $monthlySummary->getOverTimeHours());
        $this->assertEquals(20.0, $monthlySummary->getRate());
    }

    public static function provideDataForRegularHours(): array
    {
        return [
            '40 hours' => [40.0, 40.0, 0.0, 800.0],
            '30 hours' => [30.0, 30.0, 0.0, 600.0],
            '0 hours' => [0.0, 0.0, 0.0, 0.0],
            '20 hours' => [20.0, 20.0, 0.0, 400.0],
        ];
    }

    #[DataProvider('provideDataForOvertimeHours')]
    public function testCalculateFromOvertimeHours(
        float $totalHoursInMonth,
        float $expectedNormalHours,
        float $expectedOverTimeHours,
        float $expectedPayout
    ): void
    {
        $monthlySummary = new MonthlySummary($totalHoursInMonth);
        $monthlySummary->calculatePayout();

        $this->assertEquals($expectedPayout, $monthlySummary->getPayout());
        $this->assertEquals($expectedNormalHours, $monthlySummary->getNormalHours());
        $this->assertEquals($expectedOverTimeHours, $monthlySummary->getOverTimeHours());
        $this->assertEquals(20.0, $monthlySummary->getRate());
        $this->assertEquals(40.0, $monthlySummary->getOverTimeRate());
    }

    public static function provideDataForOvertimeHours(): array
    {
        return [
            '45 hours' => [45.0, 40.0, 5.0, 1000.0],
            '41 hours' => [41.0, 40.0, 1.0, 840.0],
            '50 hours' => [50.0, 40.0, 10.0, 1200.0],
            '60 hours' => [60.0, 40.0, 20.0, 1600.0]
        ];
    }
}