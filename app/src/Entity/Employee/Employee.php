<?php
declare(strict_types=1);

namespace App\Entity\Employee;

use App\Repository\Employee\EmployeeRepository;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;

#[ORM\Entity(repositoryClass: EmployeeRepository::class)]
readonly class Employee
{
    #[ORM\Id]
    #[ORM\Column(
        type: 'string',
        length: 36,
        unique: true
    )]
    public string $uuid;

    public function __construct(
        #[ORM\Column(name: 'first_name', type: 'string', length: 255)]
        public string $firstName,
        #[ORM\Column(name: 'last_name', type: 'string', length: 255)]
        public string $lastName
    )
    {
        $this->uuid = Uuid::uuid4()->toString();
    }
}