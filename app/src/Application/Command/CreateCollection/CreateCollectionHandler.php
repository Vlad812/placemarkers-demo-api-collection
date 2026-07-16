<?php

declare(strict_types=1);

namespace App\Application\Command\CreateCollection;

use App\Domain\Document\PlacemarkerSnapshot;
use App\Domain\Document\SearchCriteria;
use App\Domain\Document\UserCollection;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\MongoDBException;
use Throwable;

readonly class CreateCollectionHandler
{
    public function __construct(
        private DocumentManager $documentManager
    ) {
    }

    /**
     * @throws MongoDBException
     * @throws Throwable
     */
    public function __invoke(CreateCollectionCommand $command): string
    {
        $searchCriteria = new SearchCriteria(
            $command->latitude,
            $command->longitude,
            $command->radiusMeters
        );

        $userCollection = new UserCollection(
            $command->userUuid,
            $command->name,
            $searchCriteria
        );

        foreach ($command->placemarkers as $pmData) {
            $snapshot = new PlacemarkerSnapshot(
                $pmData['originalId'],
                $pmData['title'],
                $pmData['latitude'],
                $pmData['longitude'],
                $pmData['description'],
                $pmData['typeId'],
                $pmData['tags'],
            );
            $userCollection->addPlacemarker($snapshot);
        }

        $this->documentManager->persist($userCollection);
        $this->documentManager->flush();

        return $userCollection->getId();
    }
}
