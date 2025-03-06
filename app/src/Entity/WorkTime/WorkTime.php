<?php
declare(strict_types=1);

namespace App\Entity\WorkTime;

use App\Entity\Employee\Employee;
use App\Repository\WorkTime\WorkTimeRepository;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;

#[ORM\Entity(repositoryClass: WorkTimeRepository::class)]
#[ORM\UniqueConstraint(
    name: 'employee_date_unique',
    columns: ['employee_uuid', 'date']
)]
readonly class WorkTime
{
    #[ORM\Id]
    #[ORM\Column(
        type: 'string',
        length: 36,
        unique: true
    )]
    public string $uuid;
    #[ORM\Column(
        name: 'total_hours',
        type: 'float'
    )]
    public float $totalHours;

    public function __construct(
        #[ORM\ManyToOne(targetEntity: Employee::class)]
        #[ORM\JoinColumn(
            name: 'employee_uuid',
            referencedColumnName: 'uuid'
        )]
        public Employee $employee,

        #[ORM\Column(
            type: 'date'
        )]
        public \DateTime $date,

        #[ORM\Column(
            name: 'start_time',
            type: 'datetime_immutable'
        )]
        public \DateTimeImmutable $startTime,

        #[ORM\Column(
            name: 'end_time',
            type: 'datetime_immutable'
        )]
        public \DateTimeImmutable $endTime
    ) {
        $this->uuid = Uuid::uuid4()->toString();
        $this->totalHours = $this->roundTotalHours();
    }

    private function roundTotalHours(): float
    {
        $interval = $this->endTime->diff($this->startTime);
        $hours = $interval->h;
        $minutes = $interval->i;

        return match (true) {
            $minutes >= 45 => $hours + 1,
            $minutes >= 15 => $hours + 0.5,
            default => $hours
        };
    }
}