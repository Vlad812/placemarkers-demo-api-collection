<?php

declare(strict_types=1);

namespace App\Infrastructure\Security;

use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

final readonly class AuthenticatedUserProvider
{
    public function __construct(
        private Security $security,
    ) {
    }

    public function getUserUuid(): string
    {
        $user = $this->security->getUser();

        if (!$user instanceof User) {
            throw new AccessDeniedException('Authentication required.');
        }

        return $user->getUuid();
    }
}
