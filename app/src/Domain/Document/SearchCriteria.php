<?php

declare(strict_types=1);

namespace App\Domain\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

#[ODM\EmbeddedDocument]
class SearchCriteria
{
    #[ODM\Field(type: 'float')]
    private float $latitude;

    #[ODM\Field(type: 'float')]
    private float $longitude;

    #[ODM\Field(type: 'int')]
    private int $radiusMeters;

    public function __construct(float $latitude, float $longitude, int $radiusMeters)
    {
        $this->latitude = $latitude;
        $this->longitude = $longitude;
        $this->radiusMeters = $radiusMeters;
    }

    public function getLatitude(): float
    {
        return $this->latitude;
    }

    public function getLongitude(): float
    {
        return $this->longitude;
    }

    public function getRadiusMeters(): int
    {
        return $this->radiusMeters;
    }
}
