<?php

declare(strict_types=1);

namespace App\Tests\Application\Query\GetUserCollections;

use App\Application\Query\GetUserCollections\GetUserCollectionsQuery;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class GetUserCollectionsQueryTest extends TestCase
{
    private const string USER_UUID = '123e4567-e89b-12d3-a456-426614174000';

    public function testCreateFromRawValuesSuccess(): void
    {
        $query = GetUserCollectionsQuery::createFromRawValues([], self::USER_UUID);

        $this->assertSame(self::USER_UUID, $query->userUuid);
    }

    public function testCreateFromRawValuesInvalidUserUuidThrowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Parameter user_uuid must be a valid UUID');

        GetUserCollectionsQuery::createFromRawValues([], 'not-a-uuid');
    }
}
