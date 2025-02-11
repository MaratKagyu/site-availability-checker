<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Service\SourceListService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\ResultSetMapping;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/metrics', name: 'api_metrics_')]
class MetricsController extends ApiController
{
    public function __construct(
        private readonly SourceListService $sourceListService,
        // private readonly EntityManagerInterface $entityManager,
    )
    {
    }

    #[Route('/source_list', name: 'source_list', methods: ['GET'])]
    public function sourceList(): Response
    {
        return $this->json($this->sourceListService->getSourceList());
    }
}
