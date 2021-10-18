<?php
declare(strict_types=1);

namespace App\Subject\Domain;

use App\CustomerRepository\Domain\CustomerRepository;
use JsonSerializable;

final class Subject implements JsonSerializable
{
    private string $id;
    private string $name;
    private int $age;
    private int $height;
    private int $weight;
    private CustomerRepository $customerRepository;

    private function __construct(
        string $id,
        string $name,
        int $age,
        int $height,
        int $weight,
        CustomerRepository $customerRepository
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->age = $age;
        $this->height = $height;
        $this->weight = $weight;
        $this->customerRepository = $customerRepository;
    }

    public static function create(
        string $id,
        string $name,
        int $age,
        int $height,
        int $weight,
        CustomerRepository $customerRepository
    ): Subject {
        return new self(
            $id,
            $name,
            $age,
            $height,
            $weight,
            $customerRepository
        );
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getAge(): int
    {
        return $this->age;
    }

    public function getHeight(): int
    {
        return $this->height;
    }

    public function getWeight(): int
    {
        return $this->weight;
    }

    public function getCustomerRepository(): CustomerRepository
    {
        return $this->customerRepository;
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'age' => $this->age,
            'height' => $this->height,
            'weight' => $this->weight,
        ];
    }
}