<?php

declare(strict_types=1);

namespace App\Application\Command\DeleteCollection;

use App\Domain\Document\UserCollection;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\LockException;
use Doctrine\ODM\MongoDB\Mapping\MappingException;
use Doctrine\ODM\MongoDB\MongoDBException;
use InvalidArgumentException;
use Throwable;

readonly class DeleteCollectionHandler
{
    public function __construct(
        private DocumentManager $documentManager
    ) {
    }

    /**
     * @throws MappingException
     * @throws Throwable
     * @throws MongoDBException
     * @throws LockException
     */
    public function __invoke(DeleteCollectionCommand $command): void
    {
        $collection = $this->documentManager->getRepository(UserCollection::class)->find($command->id);

        if (!$collection) {
            throw new InvalidArgumentException('Collection not found');
        }

        if ($collection->getUserUuid() !== $command->userUuid) {
            throw new InvalidArgumentException('Access denied');
        }

        $this->documentManager->remove($collection);
        $this->documentManager->flush();
    }
}
