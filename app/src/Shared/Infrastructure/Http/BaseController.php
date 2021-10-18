<?php

namespace App\Shared\Infrastructure\Http;

use App\Shared\Application\ResponsePayload;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

abstract class BaseController
{
    protected Request $request;
    protected Response $response;
    protected array $args;

    public function __invoke(
        Request $request,
        Response $response,
        array $args
    ): Response {
        $this->request = $request;
        $this->response = $response;
        $this->args = $args;

        return $this->execute();
    }

    abstract protected function execute(): Response;

    protected function respond(ResponsePayload $payload): Response
    {
        $json = json_encode($payload, JSON_PRETTY_PRINT);
        $this->response->getBody()->write($json);

        return $this->response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus($payload->getStatusCode());
    }
}