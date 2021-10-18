<?php
declare(strict_types=1);

namespace App\Enrollment\Domain;

use App\Project\Domain\Project;
use App\Subject\Domain\Subject;

final class Enrollment
{
    public const ROL_CONTROL = 'control';
    public const ROL_RECIPIENT = 'recipient';

    private Project $project;
    private Subject $subject;
    private string $rol;

    private function __construct(
        Project $project,
        Subject $subject,
        string $rol
    ) {
        $this->project = $project;
        $this->subject = $subject;
        $this->rol = $rol;
    }

    public static function create(
        Project $project,
        Subject $subject,
        string $rol
    ): Enrollment {
        return new self(
            $project,
            $subject,
            $rol
        );
    }

    public function getProject(): Project
    {
        return $this->project;
    }

    public function getSubject(): Subject
    {
        return $this->subject;
    }

    public function getRol(): string
    {
        return $this->rol;
    }
}