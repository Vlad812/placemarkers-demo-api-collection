<?php

declare(strict_types=1);

namespace App\Tests\Application\Command\DeleteCollection;

use App\Application\Command\DeleteCollection\DeleteCollectionCommand;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class DeleteCollectionCommandTest extends TestCase
{
    private const string USER_UUID = '123e4567-e89b-12d3-a456-426614174000';

    public function testCreateFromRawValuesSuccess(): void
    {
        $command = DeleteCollectionCommand::createFromRawValues(
            ['id' => 'collection-1'],
            self::USER_UUID,
        );

        $this->assertSame('collection-1', $command->id);
        $this->assertSame(self::USER_UUID, $command->userUuid);
    }

    #[DataProvider('invalidRequestProvider')]
    public function testCreateFromRawValuesThrowsException(
        array $requestData,
        string $userUuid,
        string $expectedMessage,
    ): void {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage($expectedMessage);

        DeleteCollectionCommand::createFromRawValues($requestData, $userUuid);
    }

    public static function invalidRequestProvider(): array
    {
        return [
            'missing id' => [
                [],
                self::USER_UUID,
                'Missing required parameter: id',
            ],
            'empty id' => [
                ['id' => ''],
                self::USER_UUID,
                'Parameter id must not be empty',
            ],
            'invalid user_uuid' => [
                ['id' => 'collection-1'],
                'not-a-uuid',
                'Parameter user_uuid must be a valid UUID',
            ],
        ];
    }
}
