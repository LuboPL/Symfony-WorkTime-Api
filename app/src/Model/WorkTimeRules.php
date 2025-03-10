<?php
declare(strict_types=1);

namespace App\Model;

class WorkTimeRules
{
    public const MONTHLY_NORM = 40;
    public const DAILY_HOURS_LIMIT = 12;
    public const DEFAULT_RATE = 20.00;
    public const OVER_HOURS_RATE_MULTIPLIER = 2.0;
    public const DEFAULT_CURRENCY = 'PLN';
}