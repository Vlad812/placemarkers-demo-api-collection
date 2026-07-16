<?php

declare(strict_types=1);

namespace App\Infrastructure\Http\Action;

use App\Application\Command\CreateCollection\CreateCollectionCommand;
use App\Application\Command\CreateCollection\CreateCollectionHandler;
use App\Infrastructure\Security\AuthenticatedUserProvider;
use Doctrine\ODM\MongoDB\MongoDBException;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Throwable;

#[Route(
    '/collections',
    name: 'create_collection',
    methods: ['POST'],
)]
class CreateCollectionAction extends AbstractAction
{
    public function __construct(
        private readonly CreateCollectionHandler   $handler,
        private readonly AuthenticatedUserProvider $userProvider,
        LoggerInterface                            $logger,
    ) {
        parent::__construct($logger);
    }

    /**
     * @throws Throwable
     * @throws MongoDBException
     */
    protected function handleRequest(Request $request): Response
    {
        $command = CreateCollectionCommand::createFromRawValues(
            $this->getBody($request),
            $this->userProvider->getUserUuid(),
        );

        $id = ($this->handler)($command);

        return $this->respondJson(['status' => 'success', 'collection_id' => $id], Response::HTTP_CREATED);
    }
}
