<?php

declare(strict_types=1);

namespace App\Tests\Application\Query\GetUserCollections;

use App\Application\Query\GetUserCollections\GetUserCollectionsFetcherInterface;
use App\Application\Query\GetUserCollections\GetUserCollectionsHandler;
use App\Application\Query\GetUserCollections\GetUserCollectionsQuery;
use PHPUnit\Framework\TestCase;

final class GetUserCollectionsHandlerTest extends TestCase
{
    public function testHandleReturnsFetcherResult(): void
    {
        $query = new GetUserCollectionsQuery('123e4567-e89b-12d3-a456-426614174000');
        $expectedResult = [
            [
                'id' => 'collection-1',
                'name' => 'Favorites',
                'placemarkersCount' => 2,
            ],
        ];

        $fetcher = $this->createMock(GetUserCollectionsFetcherInterface::class);
        $fetcher->expects($this->once())
            ->method('fetch')
            ->with($query)
            ->willReturn($expectedResult);

        $handler = new GetUserCollectionsHandler($fetcher);

        $this->assertSame($expectedResult, ($handler)($query));
    }
}
