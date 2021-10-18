<?php
declare(strict_types=1);

namespace App\Shared\Application;

use JsonSerializable;

class ResponseError implements JsonSerializable
{
    private string $description;

    private function __construct(
        string $description
    ) {
        $this->description = $description;
    }

    public static function create(
        string $description
    ): ResponseError {
        return new self($description);
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function jsonSerialize(): array
    {
        return [
            'description' => $this->description,
        ];
    }
}
