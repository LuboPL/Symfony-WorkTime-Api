<?php
declare(strict_types=1);

namespace App\Model;

final class MonthlySummary implements SummaryInterface
{
    private float $payout = 0.0;
    private float $overTimeHours;
    private float $rate;
    private float $overTimeRate;
    private float $normalHours;
    private float $rateMultiplier;

    public function __construct(private readonly float $totalHoursInMonth)
    {
        $this->rate = WorkTimeRules::DEFAULT_RATE;
        if ($this->totalHoursInMonth >= WorkTimeRules::MONTHLY_NORM) {
            $this->rateMultiplier = WorkTimeRules::OVER_HOURS_RATE_MULTIPLIER;
            $this->normalHours = WorkTimeRules::MONTHLY_NORM;
            $this->overTimeRate = WorkTimeRules::DEFAULT_RATE * $this->rateMultiplier;
            $this->overTimeHours = $this->totalHoursInMonth - $this->normalHours;
        } else {
            $this->normalHours = $this->totalHoursInMonth;
            $this->rateMultiplier = 1.0;
            $this->overTimeHours = 0.0;
            $this->overTimeRate = WorkTimeRules::DEFAULT_RATE * WorkTimeRules::OVER_HOURS_RATE_MULTIPLIER;
        }
    }

    public function calculatePayout(): void
    {
        $this->payout += $this->overTimeHours * $this->overTimeRate;
        $this->payout += $this->normalHours * $this->rate;
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