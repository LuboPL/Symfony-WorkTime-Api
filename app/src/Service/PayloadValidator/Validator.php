<?php
declare(strict_types=1);

namespace App\Service\PayloadValidator;

use DateTimeImmutable;
use Webmozart\Assert\Assert;
use Webmozart\Assert\InvalidArgumentException;

class Validator
{
    private const TIME_REGEX = '/^\d{2}\.\d{2}\.\d{4} \d{2}:\d{2}$/';
    private const DAY_REGEX = '/^\d{4}-\d{2}-\d{2}$/';
    private const MONTH_REGEX = '/^\d{4}-\d{2}$/';

    /**
     * @throws InvalidArgumentException
     */
    public function validateWorkTimeRegistration(array $data, \DateTime $today): void
    {
        Assert::uuid($data['employeeUuid'], 'Invalid employee UUID format.');
        Assert::regex($data['startTime'], self::TIME_REGEX,
            'Invalid date format. Expected format: DD.MM.YYYY HH:MM'
        );

        Assert::regex($data['endTime'], self::TIME_REGEX,
            'Invalid date format. Expected format: DD.MM.YYYY HH:MM'
        );
        $startTime = DateTimeImmutable::createFromFormat('d.m.Y H:i', $data['startTime']);
        $endTime = DateTimeImmutable::createFromFormat('d.m.Y H:i', $data['endTime']);
        Assert::same($startTime->format('d.m.Y'), $today->format('d.m.Y'), 'Date must be today date');
        Assert::same($endTime->format('d.m.Y'), $today->format('d.m.Y'), 'Date must be today date');
    }

    /**
     * @throws InvalidArgumentException
     */
    public function validateDailyFormat(string $date): void
    {
        Assert::regex($date, self::DAY_REGEX, 'Invalid date format. Expected format: YYYY-MM-DD');
    }

    /**
     * @throws InvalidArgumentException
     */
    public function validateMonthlyFormat(string $date): void
    {
        Assert::regex($date, self::MONTH_REGEX, 'Invalid month format. Expected format: YYYY-MM');
    }

    /**
     * @throws InvalidArgumentException
     */
    public function validateUuid(string $uuid): void
    {
        Assert::uuid($uuid, 'Invalid employee UUID format.');
    }

    public function validateNames(array $data): void
    {
        Assert::stringNotEmpty($data['firstName'], 'First name should not be empty');
        Assert::stringNotEmpty($data['lastName'], 'Last name should not be empty');
    }
}