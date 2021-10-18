<?php
declare(strict_types=1);

namespace App\Shared\Domain;

class Criteria
{
    private array $filters;

    private function __construct(
        array $filters
    ) {
        $this->filters = $filters;
    }

    public static function create(
        array $filters
    ): Criteria {
        return new self(
            $filters
        );
    }

    public function filters(): array
    {
        return $this->filters;
    }
}