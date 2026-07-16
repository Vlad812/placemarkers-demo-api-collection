<?php

declare(strict_types=1);

namespace App\Infrastructure\Http\Action;

use App\Application\Query\GetUserCollections\GetUserCollectionsQuery;
use App\Application\Query\GetUserCollections\GetUserCollectionsHandler;
use App\Infrastructure\Security\AuthenticatedUserProvider;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route(
    '/collections',
    name: 'get_user_collections',
    methods: ['GET'],
)]
class GetUserCollectionsAction extends AbstractAction
{
    public function __construct(
        private readonly GetUserCollectionsHandler $handler,
        private readonly AuthenticatedUserProvider $userProvider,
        LoggerInterface                            $logger,
    ) {
        parent::__construct($logger);
    }

    protected function handleRequest(Request $request): Response
    {
        $query = GetUserCollectionsQuery::createFromRawValues(
            $request->query->all(),
            $this->userProvider->getUserUuid(),
        );

        $collections = ($this->handler)($query);

        return $this->respondJson(['status' => 'success', 'data' => $collections], Response::HTTP_OK);
    }
}
