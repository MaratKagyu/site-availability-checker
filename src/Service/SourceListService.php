<?php

namespace App\Service;

class SourceListService
{
    public function __construct(
        private readonly string $endpointList
    )
    {
    }

    public function getSourceList(): array
    {
        $sourceList = array_map(
            fn(string $value) => trim($value),
            explode("\n", $this->endpointList)
        );

        return array_values(array_filter(
            $sourceList,
            fn(string $value) => !!$value
        ));
    }
}
