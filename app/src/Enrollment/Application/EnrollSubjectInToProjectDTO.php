<?php
declare(strict_types=1);

namespace App\Enrollment\Application;

use App\Shared\Application\BaseDTO;

final class EnrollSubjectInToProjectDTO extends BaseDTO
{
    private string $projectId;
    private string $subjectId;
    private string $rol;

    protected function __construct(
        string $customerRepositoryId,
        string $projectId,
        string $subjectId,
        string $rol
    ) {
        parent::__construct($customerRepositoryId);

        $this->projectId = $projectId;
        $this->subjectId = $subjectId;
        $this->rol = $rol;
    }

    public static function create(
        string $customerRepositoryId,
        string $projectId,
        string $subjectId,
        string $rol
    ): EnrollSubjectInToProjectDTO {
        return new self(
            $customerRepositoryId,
            $projectId,
            $subjectId,
            $rol
        );
    }

    public function projectId(): string
    {
        return $this->projectId;
    }

    public function subjectId(): string
    {
        return $this->subjectId;
    }

    public function rol(): string
    {
        return $this->rol;
    }
}