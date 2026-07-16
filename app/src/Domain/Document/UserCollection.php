<?php

declare(strict_types=1);

namespace App\Domain\Document;

use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ODM\Document(collection: 'user_collections')]
class UserCollection
{
    #[ODM\Id]
    private ?string $id = null;

    #[ODM\Field(type: 'string')]
    #[ODM\Index]
    private string $userUuid;

    #[ODM\Field(type: 'string')]
    private string $name;

    #[ODM\Field(type: 'date')]
    #[ODM\Index(order: 'desc')]
    private DateTimeInterface $createdAt;

    #[ODM\EmbedOne(targetDocument: SearchCriteria::class)]
    private SearchCriteria $searchCriteria;

    #[ODM\EmbedMany(targetDocument: PlacemarkerSnapshot::class)]
    private Collection $placemarkers;

    public function __construct(string $userUuid, string $name, SearchCriteria $searchCriteria)
    {
        $this->userUuid = $userUuid;
        $this->name = $name;
        $this->searchCriteria = $searchCriteria;
        $this->createdAt = new DateTimeImmutable();
        $this->placemarkers = new ArrayCollection();
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getUserUuid(): string
    {
        return $this->userUuid;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getCreatedAt(): DateTimeInterface
    {
        return $this->createdAt;
    }

    public function getSearchCriteria(): SearchCriteria
    {
        return $this->searchCriteria;
    }

    public function getPlacemarkers(): Collection
    {
        return $this->placemarkers;
    }

    public function addPlacemarker(PlacemarkerSnapshot $placemarker): void
    {
        $this->placemarkers->add($placemarker);
    }
}
