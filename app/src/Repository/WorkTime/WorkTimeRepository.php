<?php
declare(strict_types=1);

namespace App\Repository\WorkTime;

use App\Entity\Employee\Employee;
use App\Entity\WorkTime\WorkTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

class WorkTimeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, WorkTime::class);
    }

    /**
     * @throws NonUniqueResultException
     */
    public function findOneByEmployeeUuidAndDate(Employee $employee, \DateTimeInterface $date): ?WorkTime
    {
        return $this->createQueryBuilder('w')
            ->andWhere('w.employee = :employee')
            ->andWhere('w.date = :date')
            ->setParameter('employee', $employee)
            ->setParameter('date', $date)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function save(WorkTime $workTime): void
    {
        $this->_em->persist($workTime);
        $this->_em->flush();
    }
}