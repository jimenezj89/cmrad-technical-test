<?php
declare(strict_types=1);

namespace App\Shared\Application;

abstract class BaseDTO
{
    protected string $customerRepositoryId;

    protected function __construct(
        string $customerRepositoryId
    ) {
        $this->customerRepositoryId = $customerRepositoryId;
    }

    public function customerRepositoryId(): string
    {
        return $this->customerRepositoryId;
    }
}