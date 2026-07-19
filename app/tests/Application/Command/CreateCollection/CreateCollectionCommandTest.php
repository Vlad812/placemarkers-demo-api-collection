<?php

declare(strict_types=1);

namespace App\Tests\Application\Command\CreateCollection;

use App\Application\Command\CreateCollection\CreateCollectionCommand;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class CreateCollectionCommandTest extends TestCase
{
    private const string USER_UUID = '123e4567-e89b-12d3-a456-426614174000';

    public function testCreateFromRawValuesSuccess(): void
    {
        $command = CreateCollectionCommand::createFromRawValues([
            'name' => 'My Collection',
            'search_criteria' => [
                'latitude' => '45.0',
                'longitude' => 90.0,
                'radius' => '1500',
            ],
            'placemarkers' => [
                [
                    'id' => 'pm-1',
                    'name' => 'Point A',
                    'lat' => '45.1',
                    'lon' => '90.1',
                    'description' => 'Test',
                    'type_id' => 'cafe',
                    'tags' => ['food', ''],
                ],
            ],
        ], self::USER_UUID);

        $this->assertSame(self::USER_UUID, $command->userUuid);
        $this->assertSame('My Collection', $command->name);
        $this->assertSame(45.0, $command->latitude);
        $this->assertSame(90.0, $command->longitude);
        $this->assertSame(1500, $command->radiusMeters);
        $this->assertCount(1, $command->placemarkers);
        $this->assertSame([
            'originalId' => 'pm-1',
            'title' => 'Point A',
            'latitude' => 45.1,
            'longitude' => 90.1,
            'description' => 'Test',
            'typeId' => 'cafe',
            'tags' => ['food'],
        ], $command->placemarkers[0]);
    }

    public function testCreateFromRawValuesUsesDefaultNameWhenMissing(): void
    {
        $command = CreateCollectionCommand::createFromRawValues([
            'search_criteria' => [
                'latitude' => 45.0,
                'longitude' => 90.0,
                'radius' => 1000,
            ],
        ], self::USER_UUID);

        $this->assertSame('Unnamed Collection', $command->name);
        $this->assertSame([], $command->placemarkers);
    }

    #[DataProvider('invalidRequestProvider')]
    public function testCreateFromRawValuesThrowsException(
        array $requestData,
        string $userUuid,
        string $expectedMessage,
    ): void {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage($expectedMessage);

        CreateCollectionCommand::createFromRawValues($requestData, $userUuid);
    }

    public static function invalidRequestProvider(): array
    {
        return [
            'invalid user_uuid' => [
                [
                    'search_criteria' => ['latitude' => 45.0, 'longitude' => 90.0, 'radius' => 1000],
                ],
                'not-a-uuid',
                'Parameter user_uuid must be a valid UUID',
            ],
            'missing search_criteria' => [
                [],
                self::USER_UUID,
                'Missing required parameter: search_criteria',
            ],
            'missing search_criteria.latitude' => [
                [
                    'search_criteria' => ['longitude' => 90.0, 'radius' => 1000],
                ],
                self::USER_UUID,
                'Missing required parameter: search_criteria.latitude',
            ],
            'placemarker without id' => [
                [
                    'search_criteria' => ['latitude' => 45.0, 'longitude' => 90.0, 'radius' => 1000],
                    'placemarkers' => [['name' => 'Point A', 'lat' => 45.0, 'lon' => 90.0]],
                ],
                self::USER_UUID,
                'Placemarker at index 0 must have originalId or id',
            ],
        ];
    }
}
