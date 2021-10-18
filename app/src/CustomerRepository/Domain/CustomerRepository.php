<?php

namespace App\CustomerRepository\Domain;

final class CustomerRepository
{
    private string $id;

    private function __construct(
        string $id
    ) {
        $this->id = $id;
    }

    public static function create(
        string $id
    ): CustomerRepository {
        return new self($id);
    }

    public function getId(): string
    {
        return $this->id;
    }
}