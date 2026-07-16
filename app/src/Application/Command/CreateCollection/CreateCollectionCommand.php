<?php

declare(strict_types=1);

namespace App\Application\Command\CreateCollection;

use App\Domain\Document\PlacemarkerSnapshot;
use Webmozart\Assert\Assert;

readonly class CreateCollectionCommand
{
    /**
     * @param list<array{
     *     originalId: string,
     *     title: string,
     *     latitude: float,
     *     longitude: float,
     *     description: ?string,
     *     typeId: string,
     *     tags: list<string>
     * }> $placemarkers
     */
    public function __construct(
        public string $userUuid,
        public string $name,
        public float $latitude,
        public float $longitude,
        public int $radiusMeters,
        public array $placemarkers,
    ) {
    }

    /**
     * @param array<string, mixed> $requestData
     */
    public static function createFromRawValues(array $requestData, string $userUuid): self
    {
        Assert::uuid($userUuid, 'Parameter user_uuid must be a valid UUID');

        $name = $requestData['name'] ?? 'Unnamed Collection';
        Assert::string($name, 'Parameter name must be a string');
        Assert::notEmpty($name, 'Parameter name must not be empty');

        Assert::keyExists($requestData, 'search_criteria', 'Missing required parameter: search_criteria');
        Assert::isArray($requestData['search_criteria'], 'Parameter search_criteria must be an array');

        $searchCriteria = $requestData['search_criteria'];
        Assert::keyExists($searchCriteria, 'latitude', 'Missing required parameter: search_criteria.latitude');
        Assert::keyExists($searchCriteria, 'longitude', 'Missing required parameter: search_criteria.longitude');
        Assert::keyExists($searchCriteria, 'radius', 'Missing required parameter: search_criteria.radius');
        Assert::numeric($searchCriteria['latitude'], 'Parameter search_criteria.latitude must be numeric');
        Assert::numeric($searchCriteria['longitude'], 'Parameter search_criteria.longitude must be numeric');
        Assert::numeric($searchCriteria['radius'], 'Parameter search_criteria.radius must be numeric');

        $placemarkers = [];
        if (isset($requestData['placemarkers'])) {
            Assert::isArray($requestData['placemarkers'], 'Parameter placemarkers must be an array');

            foreach ($requestData['placemarkers'] as $index => $placemarker) {
                Assert::isArray($placemarker, sprintf('Placemarker at index %d must be an array', $index));
                $placemarkers[] = self::normalizePlacemarker($placemarker, (int) $index);
            }
        }

        return new self(
            $userUuid,
            $name,
            (float) $searchCriteria['latitude'],
            (float) $searchCriteria['longitude'],
            (int) $searchCriteria['radius'],
            $placemarkers,
        );
    }

    /**
     * @param array<string, mixed> $placemarker
     *
     * @return array{
     *     originalId: string,
     *     title: string,
     *     latitude: float,
     *     longitude: float,
     *     description: ?string,
     *     typeId: string,
     *     tags: list<string>
     * }
     */
    private static function normalizePlacemarker(array $placemarker, int $index): array
    {
        $originalId = $placemarker['originalId'] ?? $placemarker['id'] ?? null;
        Assert::string($originalId, sprintf('Placemarker at index %d must have originalId or id', $index));
        Assert::notEmpty($originalId, sprintf('Placemarker at index %d must have non-empty originalId or id', $index));

        $title = $placemarker['title'] ?? $placemarker['name'] ?? null;
        Assert::string($title, sprintf('Placemarker at index %d must have title or name', $index));
        Assert::notEmpty($title, sprintf('Placemarker at index %d must have non-empty title or name', $index));

        $latitude = $placemarker['latitude'] ?? $placemarker['lat'] ?? null;
        Assert::notNull($latitude, sprintf('Placemarker at index %d must have latitude or lat', $index));
        Assert::numeric($latitude, sprintf('Placemarker at index %d latitude must be numeric', $index));

        $longitude = $placemarker['longitude'] ?? $placemarker['lon'] ?? null;
        Assert::notNull($longitude, sprintf('Placemarker at index %d must have longitude or lon', $index));
        Assert::numeric($longitude, sprintf('Placemarker at index %d longitude must be numeric', $index));

        $description = null;
        if (array_key_exists('description', $placemarker) && $placemarker['description'] !== null) {
            Assert::string($placemarker['description'], sprintf('Placemarker at index %d description must be a string', $index));
            $description = $placemarker['description'] !== '' ? $placemarker['description'] : null;
        }

        $typeId = PlacemarkerSnapshot::DEFAULT_TYPE;
        if (array_key_exists('typeId', $placemarker) && $placemarker['typeId'] !== null) {
            Assert::string($placemarker['typeId'], sprintf('Placemarker at index %d typeId must be a string', $index));
            $typeId = $placemarker['typeId'] !== '' ? $placemarker['typeId'] : PlacemarkerSnapshot::DEFAULT_TYPE;
        } elseif (array_key_exists('type_id', $placemarker) && $placemarker['type_id'] !== null) {
            Assert::string($placemarker['type_id'], sprintf('Placemarker at index %d type_id must be a string', $index));
            $typeId = $placemarker['type_id'] !== '' ? $placemarker['type_id'] : PlacemarkerSnapshot::DEFAULT_TYPE;
        }

        $tags = [];
        if (array_key_exists('tags', $placemarker) && $placemarker['tags'] !== null) {
            Assert::isArray($placemarker['tags'], sprintf('Placemarker at index %d tags must be an array', $index));
            Assert::allString($placemarker['tags'], sprintf('Placemarker at index %d tags must contain only strings', $index));
            $tags = array_values(array_filter(
                $placemarker['tags'],
                static fn (string $tag): bool => $tag !== '',
            ));
        }

        return [
            'originalId' => $originalId,
            'title' => $title,
            'latitude' => (float) $latitude,
            'longitude' => (float) $longitude,
            'description' => $description,
            'typeId' => $typeId,
            'tags' => $tags,
        ];
    }
}
