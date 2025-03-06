<?php
declare(strict_types=1);

namespace App\Controller\Api\V1;

use App\Entity\WorkTime\WorkTime;
use App\Repository\Employee\EmployeeRepository;
use App\Repository\WorkTime\WorkTimeRepository;
use App\Service\WorkTimeCalculator\WorkTimeCalculator;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Webmozart\Assert\Assert;
use Webmozart\Assert\InvalidArgumentException;

#[Route('/api/v1', name: 'api_v1')]
class WorkTimeController extends AbstractController
{
    private const TIME_REGEX = '/^\d{2}\.\d{2}\.\d{4} \d{2}:\d{2}$/';
    public function __construct(
        private readonly WorkTimeRepository $workTimeRepository,
        private readonly EmployeeRepository $employeeRepository,
        private readonly WorkTimeCalculator $workTimeCalculator
    )
    {
    }
    #[Route('/register_work_time', name: 'register_work_time', methods: ['POST'])]
    public function registerWorkTime(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        try {
            Assert::uuid($data['employeeUuid'], 'Invalid employee UUID format.');
            Assert::regex($data['starTime'], self::TIME_REGEX,
                'Invalid date format. Expected format: YYYY-MM-DD HH:MM'
            );
            Assert::regex($data['endTime'], self::TIME_REGEX,
                'Invalid date format. Expected format: YYYY-MM-DD HH:MM'
            );

            $employee = $this->employeeRepository->findById($data['employeeUuid']);

            $workTime = new WorkTime(
                $employee,
                new \DateTime(),
                new \DateTimeImmutable($data['starTime']),
                new \DateTimeImmutable($data['endTime']),
            );

            $this->workTimeRepository->save($workTime);
        } catch (InvalidArgumentException|\Throwable $e) {
            return $this->json([
                'response' => $e->getMessage(),
            ], 400);
        }
        return $this->json([
            'Work time added!',
        ], 201);
    }

    #[Route('/daily_summary', name: 'daily_summary', methods: ['GET'])]
    public function getDailySummary(Request $request): JsonResponse
    {
        $employeeUuid = $request->query->get('employee_uuid');
        $date = $request->query->get('date');

        try {
            Assert::uuid($employeeUuid, 'Invalid employee UUID format.');
            Assert::regex($date, '/^\d{4}-\d{2}-\d{2}$/', 'Invalid date format. Expected format: YYYY-MM-DD');

            $dateTime = DateTimeImmutable::createFromFormat('Y-m-d', $date);
            $employee = $this->employeeRepository->findById($employeeUuid);
            $dailySummary = $this->workTimeCalculator->calculatePerDay($employee, $dateTime);
            Assert::notNull($dailySummary, 'Daily summary not found.');
        } catch (InvalidArgumentException|\Throwable $e) {
            return $this->json([
                'response' => $e->getMessage(),

            ], 400);
        }


        return $this->json([
            'payout' => $dailySummary->getPayout(),
            'totalHours' => $dailySummary->hours,
            'rate' => $dailySummary->getRate()
        ]);
    }

    #[Route('/monthly_summary', name: 'monthly_summary', methods: ['GET'])]
    public function getMonthlySummary(Request $request): JsonResponse
    {
        $employeeUuid = $request->query->get('employee_uuid');
        $month = $request->query->get('month');

        try {
            Assert::uuid($employeeUuid, 'Invalid employee UUID format.');
            Assert::regex($month, '/^\d{4}-\d{2}$/', 'Invalid month format. Expected format: YYYY-MM');

            DateTimeImmutable::createFromFormat('Y-m', $month);
            $monthlySummary = $this->workTimeCalculator->calculatePerMonth();
        } catch (InvalidArgumentException $e) {
            return $this->json([
                'response' => $e->getMessage(),
            ], 400);
        }

        return $this->json([
            'normalHours' => $monthlySummary->normalHours,
            'rate' => $monthlySummary->rate,
            'overTimeHours' => $monthlySummary->overTimeHours,
            'overTimeRate' => $monthlySummary->overTimeRate,
            'payout' => $monthlySummary->payout
        ]);
    }
}