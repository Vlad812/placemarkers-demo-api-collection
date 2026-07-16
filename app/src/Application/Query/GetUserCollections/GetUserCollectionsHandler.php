<?php

declare(strict_types=1);

namespace App\Application\Query\GetUserCollections;

readonly class GetUserCollectionsHandler
{
    public function __construct(
        private GetUserCollectionsFetcherInterface $fetcher
    ) {
    }

    public function __invoke(GetUserCollectionsQuery $query): array
    {
        return $this->fetcher->fetch($query);
    }
}
