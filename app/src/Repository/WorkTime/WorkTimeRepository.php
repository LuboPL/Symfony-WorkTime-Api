<?php
declare(strict_types=1);

namespace App\Repository\WorkTime;

use App\Entity\Employee\Employee;
use App\Entity\WorkTime\WorkTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
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
    public function findOneByEmployeeAndDate(Employee $employee, \DateTimeImmutable $date): ?WorkTime
    {
        $date = $date->format('Y-m-d');
        return $this->createQueryBuilder('w')
            ->andWhere('w.employee = :employee')
            ->andWhere('w.date = :date')
            ->setParameter('employee', $employee)
            ->setParameter('date', $date)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @throws \DateMalformedStringException
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function getTotalHoursByEmployeeAndMonth(Employee $employee, \DateTimeImmutable $dateTime): float
    {
        $firstDayOfMonth = new \DateTime(
            sprintf('%s-%s-01', $dateTime->format('Y'), $dateTime->format('m'))
        );

        $firstDayOfNextMonth = clone $firstDayOfMonth;
        $firstDayOfNextMonth->modify('+1 month');

        $result = $this->createQueryBuilder('wt')
            ->select('SUM(wt.totalHours) as totalMonthHours')
            ->where('wt.employee = :employee')
            ->andWhere('wt.date >= :startDate')
            ->andWhere('wt.date < :endDate')
            ->setParameter('employee', $employee)
            ->setParameter('startDate', $firstDayOfMonth)
            ->setParameter('endDate', $firstDayOfNextMonth)
            ->getQuery()
            ->getSingleScalarResult();

        return (float)$result;
    }

    /**
     * @throws \DateMalformedStringException
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function getTotalHoursByEmployeeAndMonthUntilDay(Employee $employee, \DateTimeImmutable $dateTime): float
    {
        $firstDayOfMonth = new \DateTime(
            sprintf('%s-%s-01', $dateTime->format('Y'), $dateTime->format('m'))
        );

        $untilDay = clone $firstDayOfMonth;
        $untilDay->modify(sprintf('+%d days', $dateTime->format('d')));

        $result = $this->createQueryBuilder('wt')
            ->select('SUM(wt.totalHours) as totalMonthHours')
            ->where('wt.employee = :employee')
            ->andWhere('wt.date >= :startDate')
            ->andWhere('wt.date < :endDate')
            ->setParameter('employee', $employee)
            ->setParameter('startDate', $firstDayOfMonth)
            ->setParameter('endDate', $untilDay)
            ->getQuery()
            ->getSingleScalarResult();

        return (float)$result;
    }

    public function save(WorkTime $workTime): void
    {
        $this->_em->persist($workTime);
        $this->_em->flush();
    }
}