<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Entity\MetricsEntry;
use App\Service\AuthService;
use App\Service\SourceListService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/metrics', name: 'api_metrics_')]
class MetricsController extends ApiController
{
    public function __construct(
        private readonly SourceListService $sourceListService,
        private readonly AuthService $authService,
         private readonly EntityManagerInterface $entityManager,
    )
    {
    }

    #[Route('/source_list', name: 'source_list', methods: ['GET'])]
    public function sourceList(Request $request): Response
    {
        $this->authService->requireAuthentication($request);
        return $this->json($this->sourceListService->getSourceList());
    }

    #[Route('', name: 'save_metrics', methods: ['POST'])]
    public function saveMetrics(Request $request): Response
    {
        $this->authService->requireAuthentication($request);

        // Getting the IP address
        if ($request->server->get('HTTP_CF_CONNECTING_IP')) {
            $clientIp = $request->server->get('HTTP_CF_CONNECTING_IP');
        } elseif ($request->server->get('HTTP_X_FORWARDED_FOR')) {
            $clientIp = $request->server->get('HTTP_X_FORWARDED_FOR');
        } else {
            $clientIp = $request->getClientIp();
        }

        $requestData = $request->getPayload()->all();


        $clientName = $requestData['client'];
        foreach ($requestData['results'] as $metricData) {
            $metric = new MetricsEntry();
            $metric
                ->setClient($clientName)
                ->setUrl($metricData['endpoint'])
                ->setTimingMilliseconds($metricData['timingMilliseconds'])
                ->setCreatedDateTime(new \DateTimeImmutable())
                ->setClientIP($clientIp);

            $this->entityManager->persist($metric);
        }
        $this->entityManager->flush();
        return $this->json(["status" => "ok"]);
    }
}
