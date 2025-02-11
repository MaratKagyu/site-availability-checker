<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Service\AuthService;
use App\Service\SourceListService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\ResultSetMapping;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/metrics', name: 'api_metrics_')]
class MetricsController extends ApiController
{
    public function __construct(
        private readonly SourceListService $sourceListService,
        private readonly AuthService $authService,
        // private readonly EntityManagerInterface $entityManager,
    )
    {
    }

    #[Route('/source_list', name: 'source_list', methods: ['GET'])]
    public function sourceList(Request $request): Response
    {
        $this->authService->requireAuthentication($request);
        return $this->json($this->sourceListService->getSourceList());
    }
}
