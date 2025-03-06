<?php
declare(strict_types=1);

namespace App\Model;


final class DailySummary implements WorkTimeCalculatorInterface
{
    private ?float $payout = null;
    private ?float $rate = null;
    public function __construct(public readonly float $hours)
    {
    }

    public function calculatePayout(float $rate, float $rateMultiplier): void
    {
        $rateMultiplier == 0
            ? $this->payout = $this->hours * $rate
            : $this->payout = $this->hours * $rate * $rateMultiplier;
        $this->rate = $rate;
    }

    public function countNormalHours(): float
    {
        return $this->hours;
    }

    public function countOvertimeHours(): float
    {
        return $this->hours;
    }

    public function getPayout(): ?float
    {
        return $this->payout;
    }

    public function getRate(): ?float
    {
        return $this->rate;
    }
}