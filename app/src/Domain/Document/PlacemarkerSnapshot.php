<?php

declare(strict_types=1);

namespace App\Domain\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

#[ODM\EmbeddedDocument]
class PlacemarkerSnapshot
{
    public const string DEFAULT_TYPE = 'default';

    #[ODM\Field(type: 'string')]
    private string $originalId;

    #[ODM\Field(type: 'string')]
    private string $title;

    #[ODM\Field(type: 'float')]
    private float $latitude;

    #[ODM\Field(type: 'float')]
    private float $longitude;

    #[ODM\Field(type: 'string')]
    private ?string $description = null;

    #[ODM\Field(type: 'string')]
    private string $typeId;

    /** @var list<string> */
    #[ODM\Field(type: 'collection')]
    private array $tags = [];

    /**
     * @param list<string> $tags
     */
    public function __construct(
        string $originalId,
        string $title,
        float $latitude,
        float $longitude,
        ?string $description = null,
        string $typeId = self::DEFAULT_TYPE,
        array $tags = [],
    ) {
        $this->originalId = $originalId;
        $this->title = $title;
        $this->latitude = $latitude;
        $this->longitude = $longitude;
        $this->description = $description;
        $this->typeId = $typeId;
        $this->tags = array_values($tags);
    }

    public function getOriginalId(): string
    {
        return $this->originalId;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getLatitude(): float
    {
        return $this->latitude;
    }

    public function getLongitude(): float
    {
        return $this->longitude;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getTypeId(): string
    {
        return $this->typeId;
    }

    /**
     * @return list<string>
     */
    public function getTags(): array
    {
        return $this->tags;
    }
}
