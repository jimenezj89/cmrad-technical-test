<?php
declare(strict_types=1);

namespace App\Subject\Application;

use App\Shared\Application\BaseDTO;

final class CreateSubjectDTO extends BaseDTO
{
    private string $subjectId;
    private string $subjectName;
    private int $subjectAge;
    private int $subjectHeight;
    private int $subjectWeight;

    private function __construct(
        string $customerRepositoryId,
        string $subjectId,
        string $subjectName,
        int $subjectAge,
        int $subjectHeight,
        int $subjectWeight
    ) {
        parent::__construct($customerRepositoryId);

        $this->subjectId = $subjectId;
        $this->subjectName = $subjectName;
        $this->subjectAge = $subjectAge;
        $this->subjectHeight = $subjectHeight;
        $this->subjectWeight = $subjectWeight;
    }

    public static function create(
        string $customerRepositoryId,
        string $subjectId,
        string $subjectName,
        int $subjectAge,
        int $subjectHeight,
        int $subjectWeight
    ): CreateSubjectDTO {
        return new self(
            $customerRepositoryId,
            $subjectId,
            $subjectName,
            $subjectAge,
            $subjectHeight,
            $subjectWeight
        );
    }

    public function subjectId(): string
    {
        return $this->subjectId;
    }

    public function subjectName(): string
    {
        return $this->subjectName;
    }

    public function subjectAge(): int
    {
        return $this->subjectAge;
    }

    public function subjectHeight(): int
    {
        return $this->subjectHeight;
    }

    public function subjectWeight(): int
    {
        return $this->subjectWeight;
    }
}