<?php
declare(strict_types=1);

namespace App\Enum;

enum WorkTimeRules: int
{
    case MONTHLY_NORM = 40;
    case DEFAULT_RATE = 20;
    case RATE_MULTIPLIER = 2;
    case DAILY_HOURS_LIMIT = 12;
}