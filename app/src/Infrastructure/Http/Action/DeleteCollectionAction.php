<?php

declare(strict_types=1);

namespace App\Infrastructure\Http\Action;

use App\Application\Command\DeleteCollection\DeleteCollectionCommand;
use App\Application\Command\DeleteCollection\DeleteCollectionHandler;
use App\Infrastructure\Security\AuthenticatedUserProvider;
use Doctrine\ODM\MongoDB\LockException;
use Doctrine\ODM\MongoDB\Mapping\MappingException;
use Doctrine\ODM\MongoDB\MongoDBException;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Throwable;

#[Route(
    '/collections/{id}',
    name: 'delete_collection',
    methods: ['DELETE'],
)]
class DeleteCollectionAction extends AbstractAction
{
    public function __construct(
        private readonly DeleteCollectionHandler   $handler,
        private readonly AuthenticatedUserProvider $userProvider,
        LoggerInterface                            $logger,
    ) {
        parent::__construct($logger);
    }

    /**
     * @throws Throwable
     * @throws MappingException
     * @throws MongoDBException
     * @throws LockException
     */
    protected function handleRequest(Request $request): Response
    {
        $command = DeleteCollectionCommand::createFromRawValues(
            ['id' => $request->attributes->get('id')],
            $this->userProvider->getUserUuid(),
        );

        ($this->handler)($command);

        return $this->respondJson(['status' => 'success'], Response::HTTP_OK);
    }
}
