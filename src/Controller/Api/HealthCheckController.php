<?php

declare(strict_types=1);

namespace App\Controller\Api;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\ResultSetMapping;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/health', name: 'api_health_')]
class HealthCheckController extends ApiController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
    )
    {
    }

    #[Route('', name: 'index', methods: ['GET'])]
    public function healthCheck(): Response
    {
        $this->entityManager
            ->createNativeQuery("SELECT 'Hello World'", new ResultSetMapping())
            ->getResult();

        return $this->json([
            "status" => "ok",
            "message" => "healthy"
        ]);
    }

    #[Route('/http500', name: 'http500', methods: ['GET'])]
    public function generateHttp500(): Response
    {
        throw new \Exception(
            'Test Internal server error',
            500
        );
    }
}
