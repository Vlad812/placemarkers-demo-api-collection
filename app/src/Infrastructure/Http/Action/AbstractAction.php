<?php

declare(strict_types=1);

namespace App\Infrastructure\Http\Action;

use App\Domain\Exception\InvalidCoordinatesException;
use InvalidArgumentException;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Exception\JsonException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Throwable;

abstract class AbstractAction
{
    public function __construct(
        protected readonly LoggerInterface $logger,
    ) {
    }

    abstract protected function handleRequest(Request $request): Response;

    public function __invoke(Request $request): Response
    {
        try {
            return $this->handleRequest($request);
        } catch (
            InvalidArgumentException|
            InvalidCoordinatesException|
            UnprocessableEntityHttpException $exception
        ) {
            $this->logger->error(
                sprintf('Validation failed. Error: [%s], Message: [%s].', $exception::class, $exception->getMessage()),
                ['exception' => $exception],
            );

            return $this->respondJson(['errors' => [['message' => $exception->getMessage()]]], Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (JsonException|BadRequestHttpException $exception) {
            $this->logger->error(
                sprintf('Bad request. Error: [%s], Message: [%s].', $exception::class, $exception->getMessage()),
                ['exception' => $exception],
            );

            return $this->respondJson(['errors' => [['message' => $exception->getMessage()]]], Response::HTTP_BAD_REQUEST);
        } catch (Throwable $exception) {
            $this->logger->error(
                sprintf('Unexpected error. Error: [%s], Message: [%s].', $exception::class, $exception->getMessage()),
                ['exception' => $exception],
            );

            return $this->respondJson(['errors' => [['message' => 'Internal server error.']]], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @return array<string, mixed>
     *
     * @throws JsonException
     */
    protected function getBody(Request $request): array
    {
        return $request->toArray();
    }

    /**
     * @param array<string, mixed>|list<mixed>|null $data
     */
    protected function respondJson(?array $data, int $status = Response::HTTP_OK): JsonResponse
    {
        return new JsonResponse($data, $status);
    }
}
