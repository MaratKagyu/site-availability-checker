<?php

declare(strict_types=1);

namespace App\Controller\Api;

use Doctrine\ORM\Query;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

class ApiController extends AbstractController
{
    protected const DEFAULT_PAGE = 1;
    protected const DEFAULT_LIMIT = 10;

    public function jsonWithGroup(
        mixed $data,
        int $status = 200,
        array $headers = [],
        array $groups = []
    ): JsonResponse
    {
        return $this->json(
            $data,
            $status,
            $headers,
            [ "groups" => $groups ]
        );
    }

    public function paginationData(
        Query $query,
        int $page = self::DEFAULT_PAGE,
        int $limit = self::DEFAULT_LIMIT,
        string $wrapper = null
    ): array
    {
        $paginator = new Paginator($query, fetchJoinCollection: true);
        $paginator->getQuery()
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit);

        $total = $paginator->count();
        $pageCount = ceil($total / $limit) ?: 1;

        $itemList = [];
        foreach ($paginator as $item) {
            $wrapperWithItem = $wrapper ? new $wrapper($item) : $item;
            $itemList[] = $wrapperWithItem;
        }

        return [
            'page' => $page,
            'total' => $total,
            'pageCount' => $pageCount,
            'itemList' => $itemList,
        ];
    }
}
