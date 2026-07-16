<?php

declare(strict_types=1);

namespace App\Application\Command\DeleteCollection;

use Webmozart\Assert\Assert;

readonly class DeleteCollectionCommand
{
    public function __construct(
        public string $id,
        public string $userUuid,
    ) {
    }

    /**
     * @param array<string, mixed> $requestData
     */
    public static function createFromRawValues(array $requestData, string $userUuid): self
    {
        Assert::keyExists($requestData, 'id', 'Missing required parameter: id');
        Assert::string($requestData['id'], 'Parameter id must be a string');
        Assert::notEmpty($requestData['id'], 'Parameter id must not be empty');
        Assert::uuid($userUuid, 'Parameter user_uuid must be a valid UUID');

        return new self($requestData['id'], $userUuid);
    }
}
