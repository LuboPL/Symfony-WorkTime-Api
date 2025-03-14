<?php
declare(strict_types=1);

namespace App\Controller\Api\V1;

use App\Service\Employee\EmployeeService;
use App\Service\PayloadValidator\Validator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Webmozart\Assert\InvalidArgumentException;

#[Route('/api/v1', name: 'api_v1')]
class EmployeeController extends AbstractController
{
    public function __construct(
        private readonly Validator $validator,
        private readonly EmployeeService $employeeService
    )
    {
    }

    #[Route('/create_employee', name: 'create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        try {
            $this->validator->validateNames($data);
            $employee = $this->employeeService->createEmployee($data);
            $this->employeeService->saveEmployee($employee);
        } catch (InvalidArgumentException|\Throwable $e) {
            return $this->json([
                'response' => $e->getMessage(),
            ], 400);
        }
        return $this->json([
            'id' => $employee->uuid,

        ], 201);
    }
}