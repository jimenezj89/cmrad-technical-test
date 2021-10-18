<?php
declare(strict_types=1);

namespace App\Shared\Infrastructure\Slim\Handlers;

use App\Shared\Application\ResponseError;
use App\Shared\Application\ResponsePayload;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Handlers\ErrorHandler as SlimErrorHandler;

class HttpErrorHandler extends SlimErrorHandler
{
    protected function respond(): Response
    {
        $statusCode = 500;
        $error = ResponseError::create(
            'An internal error has occurred while processing your request.'
        );

        $payload = ResponsePayload::create($statusCode, null, $error);
        $encodedPayload = json_encode($payload, JSON_PRETTY_PRINT);

        $response = $this->responseFactory->createResponse($statusCode);
        $response->getBody()->write($encodedPayload);

        return $response->withHeader('Content-Type', 'application/json');
    }
}
