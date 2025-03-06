<?php
declare(strict_types=1);

namespace App\Model;

interface WorkTimeCalculatorInterface
{
    public function calculatePayout(float $rate, float $rateMultiplier): void;
    public function countOvertimeHours(): void;
}