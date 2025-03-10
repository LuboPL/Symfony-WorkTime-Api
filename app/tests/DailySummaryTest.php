<?php
declare(strict_types=1);

namespace App\Tests;

use App\Model\DailySummary;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class DailySummaryTest extends TestCase
{
    #[DataProvider('provideDataForRegularHours')]
    public function testCalculateFromRegularHours(
        float $hoursInDay,
        float $totalHoursInMonth,
        float $expectedRate,
        float $expectedPayout
    ): void
    {
        $dailySummary = new DailySummary($hoursInDay, $totalHoursInMonth);
        $dailySummary->calculatePayout();

        $this->assertEquals($expectedRate, $dailySummary->getRate());
        $this->assertEquals($expectedPayout, $dailySummary->getPayout());
        $this->assertEquals($hoursInDay, $dailySummary->getHoursInDay());
    }

    public static function provideDataForRegularHours(): array
    {
        return [
            '8 hours with 20 total' => [8.0, 20.0, 20.0, 160.0],
            '4 hours with 30 total' => [4.0, 30.0, 20.0, 80.0],
            '0 hours with 0 total' => [0.0, 0.0, 20.0, 0.0],
            '7.5 hours with 39.5 total' => [7.5, 39.5, 20.0, 150.0],
        ];
    }

    #[DataProvider('provideDataForOvertimeHours')]
    public function testCalculateFromOvertimeHours(
        float $hoursInDay,
        float $totalHoursInMonth,
        float $expectedRate,
        float $expectedPayout
    ): void
    {
        $dailySummary = new DailySummary($hoursInDay, $totalHoursInMonth);
        $dailySummary->calculatePayout();

        $this->assertEquals($expectedRate, $dailySummary->getRate());
        $this->assertEquals($expectedPayout, $dailySummary->getPayout());
        $this->assertEquals($hoursInDay, $dailySummary->getHoursInDay());
    }

    public static function provideDataForOvertimeHours(): array
    {
        return [
            '8 hours with 41 total' => [8.0, 41.0, 40.0, 320.0],
            '4 hours with 45 total' => [4.0, 45.0, 40.0, 160.0],
            '10 hours with 50 total' => [10.0, 50.0, 40.0, 400.0],
            '7.5 hours with 40.1 total' => [7.5, 40.1, 40.0, 300.0],
        ];
    }
}
