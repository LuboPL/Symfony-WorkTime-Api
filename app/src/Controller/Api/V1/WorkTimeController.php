<?php
declare(strict_types=1);

namespace App\Controller\Api\V1;

use App\Entity\WorkTime\WorkTime;
use App\Model\WorkTimeRules;
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
            $today = new \DateTime();

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

            $employee = $this->employeeRepository->findById($data['employeeUuid']);

            $workTime = new WorkTime(
                $employee,
                $today,
                $startTime,
                $endTime,
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
            'payout' => sprintf('%d %s',$dailySummary->getPayout(), WorkTimeRules::DEFAULT_CURRENCY),
            'totalHours' => $dailySummary->getHoursInDay(),
            'rate' => sprintf('%s %s', $dailySummary->getRate(), WorkTimeRules::DEFAULT_CURRENCY),
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

            $employee = $this->employeeRepository->findById($employeeUuid);

            $date = DateTimeImmutable::createFromFormat('Y-m', $month);
            $monthlySummary = $this->workTimeCalculator->calculatePerMonth($employee, $date);
        } catch (InvalidArgumentException|\Throwable $e) {
            return $this->json([
                'response' => $e->getMessage(),
            ], 400);
        }

        return $this->json([
            'normalHours' => $monthlySummary->getNormalHours(),
            'rate' => sprintf('%s %s', $monthlySummary->getRate(), WorkTimeRules::DEFAULT_CURRENCY),
            'overTimeHours' => $monthlySummary->getOverTimeHours(),
            'overTimeRate' => sprintf('%s %s', $monthlySummary->getOverTimeRate(), WorkTimeRules::DEFAULT_CURRENCY),
            'payout' => sprintf('%s %s', $monthlySummary->getPayout(), WorkTimeRules::DEFAULT_CURRENCY),
        ]);
    }
}