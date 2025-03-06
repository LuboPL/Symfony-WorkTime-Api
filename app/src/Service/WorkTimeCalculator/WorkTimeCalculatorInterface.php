<?php
declare(strict_types=1);

namespace App\Service\WorkTimeCalculator;

interface WorkTimeCalculatorInterface
{
    public function calculatePayout(): float;
    public function countNormalHours(): float;
    public function countOvertimeHours(): float;
}