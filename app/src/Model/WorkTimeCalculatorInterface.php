<?php
declare(strict_types=1);

namespace App\Model;

interface WorkTimeCalculatorInterface
{
    public function calculatePayout(float $rate, float $rateMultiplier): void;
    public function countNormalHours(): float;
    public function countOvertimeHours(): float;
}