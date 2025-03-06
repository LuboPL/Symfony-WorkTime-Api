<?php
declare(strict_types=1);

namespace App\Controller\Api\V1;

use App\Entity\Employee\Employee;
use App\Repository\Employee\EmployeeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Webmozart\Assert\Assert;
use Webmozart\Assert\InvalidArgumentException;

#[Route('/api/v1', name: 'api_v1')]
class EmployeeController extends AbstractController
{
    public function __construct(private readonly EmployeeRepository $employeeRepository)
    {
    }

    #[Route('/create_employee', name: 'create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        try {
            Assert::stringNotEmpty($data['firstName'], 'First name should not be empty');
            Assert::stringNotEmpty($data['lastName'], 'Last name should not be empty');

            $employee = new Employee($data['firstName'], $data['lastName']);
            $this->employeeRepository->save($employee);
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