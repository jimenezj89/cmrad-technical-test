<?php

namespace App\Shared\Infrastructure\Persistence;

use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\ResponseInterface;

abstract class HttpBaseRepository
{
    protected const GET = 'GET';
    protected const POST = 'POST';

    protected ClientInterface $client;

    public function __construct(
        ClientInterface $client
    ) {
        $this->client = $client;
    }

    protected function request(
        string $method,
        string $endpoint,
        array $options = []
    ): ResponseInterface {

        /**
         * Authorization headers will be included here
         *
         * ...
         */

        return $this->client->request(
            $method,
            $endpoint,
            $options
        );
    }
}