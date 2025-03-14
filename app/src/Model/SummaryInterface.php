<?php
declare(strict_types=1);

namespace App\Model;

interface SummaryInterface
{
    public function calculatePayout(): void;
    public function getPayload(): array;
}