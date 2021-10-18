<?php
declare(strict_types=1);

namespace App\Shared\Application;

use JsonSerializable;

class ResponsePayload implements JsonSerializable
{
    private int $statusCode;
    /** @param array|object|null */
    private $data;
    private ?ResponseError $error;

    /**
     * @param array|object|null $data
     */
    private function __construct(
        int $statusCode = 200,
        $data = null,
        ?ResponseError $error = null
    ) {
        $this->statusCode = $statusCode;
        $this->data = $data;
        $this->error = $error;
    }

    /**
     * @param array|object|null $data
     */
    public static function create(
        int $statusCode = 200,
        $data = null,
        ?ResponseError $error = null
    ): ResponsePayload {
        return new self(
            $statusCode,
            $data,
            $error
        );
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function getError(): ?ResponseError
    {
        return $this->error;
    }

    public function jsonSerialize(): array
    {
        $payload = [
            'statusCode' => $this->statusCode,
        ];

        if ($this->data !== null) {
            $payload['data'] = $this->data;
        } elseif ($this->error !== null) {
            $payload['error'] = $this->error;
        }

        return $payload;
    }
}
