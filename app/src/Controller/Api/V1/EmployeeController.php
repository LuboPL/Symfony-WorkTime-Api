<?php
declare(strict_types=1);

namespace App\Controller\Api\V1;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/v1', name: 'api_v1')]
class EmployeeController extends AbstractController
{
    #[Route('/create_employee', name: 'create', methods: ['POST'])]
    public function create(): JsonResponse
    {
        return $this->json([

        ]);
    }
}