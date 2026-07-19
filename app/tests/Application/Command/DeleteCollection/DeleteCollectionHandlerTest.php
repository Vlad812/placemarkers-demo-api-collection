<?php

declare(strict_types=1);

namespace App\Tests\Application\Command\DeleteCollection;

use App\Application\Command\DeleteCollection\DeleteCollectionCommand;
use App\Application\Command\DeleteCollection\DeleteCollectionHandler;
use App\Domain\Document\SearchCriteria;
use App\Domain\Document\UserCollection;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\Repository\DocumentRepository;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class DeleteCollectionHandlerTest extends TestCase
{
    private const string USER_UUID = '123e4567-e89b-12d3-a456-426614174000';

    public function testInvokeRemovesCollectionWhenOwnedByUser(): void
    {
        $command = new DeleteCollectionCommand('collection-1', self::USER_UUID);
        $collection = new UserCollection(
            self::USER_UUID,
            'Favorites',
            new SearchCriteria(45.0, 90.0, 1000),
        );

        $repository = $this->createMock(DocumentRepository::class);
        $repository->expects($this->once())
            ->method('find')
            ->with('collection-1')
            ->willReturn($collection);

        $documentManager = $this->createMock(DocumentManager::class);
        $documentManager->expects($this->once())
            ->method('getRepository')
            ->with(UserCollection::class)
            ->willReturn($repository);
        $documentManager->expects($this->once())
            ->method('remove')
            ->with($collection);
        $documentManager->expects($this->once())->method('flush');

        $handler = new DeleteCollectionHandler($documentManager);

        $handler($command);
    }

    public function testInvokeThrowsWhenCollectionNotFound(): void
    {
        $command = new DeleteCollectionCommand('collection-1', self::USER_UUID);

        $repository = $this->createMock(DocumentRepository::class);
        $repository->expects($this->once())->method('find')->willReturn(null);

        $documentManager = $this->createMock(DocumentManager::class);
        $documentManager->expects($this->once())
            ->method('getRepository')
            ->willReturn($repository);
        $documentManager->expects($this->never())->method('remove');

        $handler = new DeleteCollectionHandler($documentManager);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Collection not found');

        $handler($command);
    }

    public function testInvokeThrowsWhenAccessDenied(): void
    {
        $command = new DeleteCollectionCommand('collection-1', self::USER_UUID);
        $collection = new UserCollection(
            '999e4567-e89b-12d3-a456-426614174999',
            'Favorites',
            new SearchCriteria(45.0, 90.0, 1000),
        );

        $repository = $this->createMock(DocumentRepository::class);
        $repository->expects($this->once())->method('find')->willReturn($collection);

        $documentManager = $this->createMock(DocumentManager::class);
        $documentManager->expects($this->once())
            ->method('getRepository')
            ->willReturn($repository);
        $documentManager->expects($this->never())->method('remove');

        $handler = new DeleteCollectionHandler($documentManager);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Access denied');

        $handler($command);
    }
}
