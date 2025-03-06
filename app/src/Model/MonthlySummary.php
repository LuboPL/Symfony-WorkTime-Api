<?php
declare(strict_types=1);

namespace App\Model;

use App\Enum\WorkTimeRules;

final class MonthlySummary implements WorkTimeCalculatorInterface
{
    private float $payout;
    private ?float $overTimeHours = null;
    private float $rate;
    private float $overTimeRate;
    private float $normalHours;

    public function __construct(private readonly float $totalHours)
    {
        $this->overTimeRate = (float)WorkTimeRules::DEFAULT_RATE->value * (float)WorkTimeRules::RATE_MULTIPLIER->value;
        
        if ($this->totalHours > WorkTimeRules::MONTHLY_NORM->value) {
            $this->normalHours = (float)WorkTimeRules::MONTHLY_NORM->value;
        } else {
            $this->normalHours = $totalHours;
        }
    }

    public function calculatePayout(float $rate, float $rateMultiplier): void
    {
        $this->rate = $rate;
        $this->payout = $rate * $this->normalHours;

        if ($rateMultiplier != 0) {
            $this->payout += $rate * $this->normalHours * $rateMultiplier;
            $this->overTimeRate = $this->rate * $rateMultiplier;
        }
    }

    public function countOvertimeHours(): void
    {
        $this->overTimeHours = $this->totalHours - $this->normalHours;
    }

    public function getPayout(): float
    {
        return $this->payout;
    }

    public function getOverTimeHours(): ?float
    {
        return $this->overTimeHours;
    }

    public function getRate(): float
    {
        return $this->rate;
    }

    public function getOverTimeRate(): ?float
    {
        return $this->overTimeRate;
    }

    public function getNormalHours(): float
    {
        return $this->normalHours;
    }
}