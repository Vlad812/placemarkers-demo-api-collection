<?php

declare(strict_types=1);

namespace App\Application\Query\GetUserCollections;

interface GetUserCollectionsFetcherInterface
{
    public function fetch(GetUserCollectionsQuery $query): array;
}
