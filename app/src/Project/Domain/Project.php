<?php
declare(strict_types=1);

namespace App\Project\Domain;

final class Project
{
    private string $id;

    private function __construct(
        string $id
    ) {
        $this->id = $id;
    }

    public static function create(
        string $id
    ): Project {
        return new self($id);
    }

    public function getId(): string
    {
        return $this->id;
    }
}