<?php
declare(strict_types=1);

namespace App\Model;
final readonly class MonthlySummary implements WorkTimeCalculatorInterface
{
    public function __construct(
        public float $payout,
        public float $normalHours,
        public float $overTimeHours,
        public float $rate,
        public float $overTimeRate
    )
    {
    }

    public function calculatePayout(float $rate, float $rateMultiplier): void
    {
        // TODO: Implement calculatePayout() method.
    }

    public function countNormalHours(): float
    {
        // TODO: Implement countNormalHours() method.
    }

    public function countOvertimeHours(): float
    {
        // TODO: Implement countOvertimeHours() method.
    }
}