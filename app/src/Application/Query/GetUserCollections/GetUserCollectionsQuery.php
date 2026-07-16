<?php

declare(strict_types=1);

namespace App\Application\Query\GetUserCollections;

use Webmozart\Assert\Assert;

readonly class GetUserCollectionsQuery
{
    public function __construct(
        public string $userUuid,
    ) {
    }

    /**
     * @param array<string, mixed> $requestData
     */
    public static function createFromRawValues(array $requestData, string $userUuid): self
    {
        Assert::uuid($userUuid, 'Parameter user_uuid must be a valid UUID');

        return new self($userUuid);
    }
}
