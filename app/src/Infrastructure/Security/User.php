<?php

declare(strict_types=1);

namespace App\Infrastructure\Security;

use Lexik\Bundle\JWTAuthenticationBundle\Security\User\JWTUserInterface;

final class User implements JWTUserInterface
{
    /**
     * @param string[] $roles
     */
    public function __construct(
        private string $userIdentifier,
        private string $uuid,
        private array $roles = [],
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public static function createFromPayload($username, array $payload): self
    {
        $roles = $payload['roles'] ?? [];
        $uuid = $payload['uuid'] ?? '';

        return new self($username, (string) $uuid, (array) $roles);
    }

    public function getUserIdentifier(): string
    {
        return $this->userIdentifier;
    }

    public function getUuid(): string
    {
        return $this->uuid;
    }

    /**
     * {@inheritdoc}
     */
    public function getRoles(): array
    {
        return $this->roles;
    }

    public function getPassword(): ?string
    {
        return null;
    }

    public function getSalt(): ?string
    {
        return null;
    }

    public function eraseCredentials(): void
    {
    }
}
