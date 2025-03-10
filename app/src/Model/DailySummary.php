<?php
declare(strict_types=1);

namespace App\Model;

final class DailySummary implements SummaryInterface
{
    private float $payout = 0.0;
    private float $rateMultiplier;
    private float $rate;
    public function __construct(private ?float $hoursInDay, private readonly float $totalHoursInMonth)
    {
        $this->hoursInDay ??= 0.0;

        $this->rateMultiplier = $this->totalHoursInMonth > WorkTimeRules::MONTHLY_NORM
            ? WorkTimeRules::OVER_HOURS_RATE_MULTIPLIER
            : 1.0;
        $this->rate = WorkTimeRules::DEFAULT_RATE * $this->rateMultiplier;
    }

    public function calculatePayout(): void
    {
        $this->payout = $this->hoursInDay * $this->rate;
    }

    public function getPayout(): float
    {
        return $this->payout;
    }

    public function getRate(): float
    {
        return $this->rate;
    }

    public function getHoursInDay(): float
    {
        return $this->hoursInDay;
    }
}