<?php

declare(strict_types=1);

namespace App\Tests\Application\Command\CreateCollection;

use App\Application\Command\CreateCollection\CreateCollectionCommand;
use App\Application\Command\CreateCollection\CreateCollectionHandler;
use App\Domain\Document\UserCollection;
use Doctrine\ODM\MongoDB\DocumentManager;
use PHPUnit\Framework\TestCase;

final class CreateCollectionHandlerTest extends TestCase
{
    public function testHandlePersistsAndFlushesCollection(): void
    {
        $command = new CreateCollectionCommand(
            userUuid: '123e4567-e89b-12d3-a456-426614174000',
            name: 'Favorites',
            latitude: 45.0,
            longitude: 90.0,
            radiusMeters: 1000,
            placemarkers: [
                [
                    'originalId' => 'pm-1',
                    'title' => 'Point A',
                    'latitude' => 45.1,
                    'longitude' => 90.1,
                    'description' => null,
                    'typeId' => 'default',
                    'tags' => [],
                ],
            ],
        );

        $documentManager = $this->createMock(DocumentManager::class);
        $documentManager->expects($this->once())
            ->method('persist')
            ->with($this->callback(static function (UserCollection $collection): bool {
                $idProperty = new \ReflectionProperty(UserCollection::class, 'id');
                $idProperty->setValue($collection, 'collection-id');

                return $collection->getName() === 'Favorites'
                    && $collection->getUserUuid() === '123e4567-e89b-12d3-a456-426614174000'
                    && $collection->getPlacemarkers()->count() === 1;
            }));
        $documentManager->expects($this->once())->method('flush');

        $handler = new CreateCollectionHandler($documentManager);

        $this->assertSame('collection-id', ($handler)($command));
    }
}
