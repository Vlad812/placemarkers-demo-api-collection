<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine\Fetcher;

use App\Application\Query\GetUserCollections\GetUserCollectionsFetcherInterface;
use App\Application\Query\GetUserCollections\GetUserCollectionsQuery;
use App\Domain\Document\UserCollection;
use DateTimeInterface;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\MongoDBException;

readonly class DoctrineGetUserCollectionsFetcher implements GetUserCollectionsFetcherInterface
{
    public function __construct(
        private DocumentManager $documentManager
    ) {
    }

    /**
     * @throws MongoDBException
     */
    public function fetch(GetUserCollectionsQuery $query): array
    {
        $qb = $this->documentManager->createQueryBuilder(UserCollection::class)
            ->field('userUuid')->equals($query->userUuid)
            ->sort('createdAt', 'desc');

        $cursor = $qb->getQuery()->execute();

        $result = [];
        foreach ($cursor as $document) {
            $placemarkers = [];
            foreach ($document->getPlacemarkers() as $pm) {
                $placemarker = [
                    'originalId' => $pm->getOriginalId(),
                    'title' => $pm->getTitle(),
                    'latitude' => $pm->getLatitude(),
                    'longitude' => $pm->getLongitude(),
                    'typeId' => $pm->getTypeId(),
                ];

                if ($pm->getDescription() !== null) {
                    $placemarker['description'] = $pm->getDescription();
                }

                if ($pm->getTags() !== []) {
                    $placemarker['tags'] = $pm->getTags();
                }

                $placemarkers[] = $placemarker;
            }

            $result[] = [
                'id' => $document->getId(),
                'name' => $document->getName(),
                'createdAt' => $document->getCreatedAt()->format(DateTimeInterface::ATOM),
                'searchCriteria' => [
                    'latitude' => $document->getSearchCriteria()->getLatitude(),
                    'longitude' => $document->getSearchCriteria()->getLongitude(),
                    'radiusMeters' => $document->getSearchCriteria()->getRadiusMeters(),
                ],
                'placemarkersCount' => count($placemarkers),
                'placemarkers' => $placemarkers,
            ];
        }

        return $result;
    }
}
