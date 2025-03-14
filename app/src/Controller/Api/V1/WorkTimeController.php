<?php
declare(strict_types=1);

namespace App\Controller\Api\V1;

use App\Service\PayloadValidator\Validator;
use App\Service\WorkTime\WorkTimeService;
use App\Service\WorkTimeCalculator\WorkTimeCalculator;
use DateTime;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Webmozart\Assert\InvalidArgumentException;

#[Route('/api/v1', name: 'api_v1')]
class WorkTimeController extends AbstractController
{
    private DateTime $today;

    public function __construct(
        private readonly Validator $validator,
        private readonly WorkTimeService $workTimeService,
        private readonly WorkTimeCalculator $workTimeCalculator
    )
    {
        $this->today = new DateTime();
    }

    #[Route('/register_work_time', name: 'register_work_time', methods: ['POST'])]
    public function registerWorkTime(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        try {
            $this->validator->validateWorkTimeRegistration($data, $this->today);
            $workTime = $this->workTimeService->createWorkTime($data, $this->today);
            $this->workTimeService->saveWorkTime($workTime);
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
            $this->validator->validateUuid($employeeUuid);
            $this->validator->validateDailyFormat($date);
            $dailySummary = $this->workTimeCalculator->calculatePerDay(
                $this->workTimeService->getEmployee($employeeUuid),
                DateTimeImmutable::createFromFormat('Y-m-d', $date)
            );
        } catch (InvalidArgumentException|\Throwable $e) {
            return $this->json([
                'response' => $e->getMessage(),

            ], 400);
        }

        return $this->json($dailySummary->getPayload());
    }

    #[Route('/monthly_summary', name: 'monthly_summary', methods: ['GET'])]
    public function getMonthlySummary(Request $request): JsonResponse
    {
        $employeeUuid = $request->query->get('employee_uuid');
        $month = $request->query->get('month');

        try {
            $this->validator->validateUuid($employeeUuid);
            $this->validator->validateMonthlyFormat($month);
            $monthlySummary = $this->workTimeCalculator->calculatePerMonth(
                $this->workTimeService->getEmployee($employeeUuid),
                DateTimeImmutable::createFromFormat('Y-m', $month)
            );
        } catch (InvalidArgumentException|\Throwable $e) {
            return $this->json([
                'response' => $e->getMessage(),
            ], 400);
        }

        return $this->json($monthlySummary->getPayload());
    }
}