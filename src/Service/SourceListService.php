<?php

namespace App\Service;

class SourceListService
{
    public function __construct(
        private readonly string $endpointList,
    )
    {
    }

    public function getSourceList(): array
    {
        $sourceList = array_map(
            fn(string $value) => trim($value),
            explode("\n", $this->endpointList)
        );

        $sourceList = array_values(array_filter(
            $sourceList,
            fn(string $value) => !!$value
        ));

        return array_map(
            function(string $value) {
                $valueList = explode(" - ", $value);
                return [
                    "name" => $valueList[1] ?? $valueList[0],
                    "endpoint" => $valueList[0],
                ];
            },
            $sourceList
        );
    }
}
