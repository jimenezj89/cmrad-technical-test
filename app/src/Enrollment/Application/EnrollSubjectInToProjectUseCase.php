<?php
declare(strict_types=1);

namespace App\Enrollment\Application;

use App\Enrollment\Domain\Enrollment;
use App\Enrollment\Domain\EnrollmentAlreadyExistsException;
use App\Enrollment\Domain\EnrollmentsRepositoryInterface;
use App\Project\Domain\Project;
use App\Project\Domain\ProjectNotFoundException;
use App\Project\Domain\ProjectsRepositoryInterface;
use App\Shared\Domain\Criteria;
use App\Subject\Domain\Subject;
use App\Subject\Domain\SubjectNotFoundException;
use App\Subject\Domain\SubjectsRepositoryInterface;

final class EnrollSubjectInToProjectUseCase
{
    private ProjectsRepositoryInterface $projectsRepository;
    private SubjectsRepositoryInterface $subjectsRepository;
    private EnrollmentsRepositoryInterface $enrollmentsRepository;

    public function __construct(
        ProjectsRepositoryInterface $projectsRepository,
        SubjectsRepositoryInterface $subjectsRepository,
        EnrollmentsRepositoryInterface  $enrollmentsRepository
    ) {
        $this->projectsRepository = $projectsRepository;
        $this->subjectsRepository = $subjectsRepository;
        $this->enrollmentsRepository = $enrollmentsRepository;
    }

    /**
     * @throws EnrollmentAlreadyExistsException
     * @throws ProjectNotFoundException
     * @throws SubjectNotFoundException
     */
    public function __invoke(EnrollSubjectInToProjectDTO $dto): Enrollment
    {
        $project = $this->findProjectOrFail(
            $dto->customerRepositoryId(),
            $dto->projectId()
        );
        $subject = $this->findSubjectOrFail(
            $dto->customerRepositoryId(),
            $dto->subjectId()
        );
        $this->subjectAlreadyEnrolledGuard(
            $dto->customerRepositoryId(),
            $dto->projectId(),
            $dto->subjectId(),
        );

        $enrollment = Enrollment::create(
            $project,
            $subject,
            $dto->rol()
        );

        $this->enrollmentsRepository->create($enrollment);

        return $enrollment;
    }

    /**
     * @throws ProjectNotFoundException
     */
    private function findProjectOrFail(
        string $customerRepositoryId,
        string $projectId
    ): Project {
        $criteria = Criteria::create([
            'customerRepositoryId' => $customerRepositoryId,
            'projectId' => $projectId,
        ]);

        $project = $this->projectsRepository->findOneByCriteria($criteria);

        if (is_null($project)) {
            throw new ProjectNotFoundException();
        }

        return $project;
    }

    /**
     * @throws SubjectNotFoundException
     */
    private function findSubjectOrFail(
        string $customerRepositoryId,
        string $subjectId
    ): Subject {
        $criteria = Criteria::create([
            'customerRepositoryId' => $customerRepositoryId,
            'subjectId' => $subjectId,
        ]);

        $subject = $this->subjectsRepository->findOneByCriteria($criteria);

        if (is_null($subject)) {
            throw new SubjectNotFoundException();
        }

        return $subject;
    }

    /**
     * @throws EnrollmentAlreadyExistsException
     */
    private function subjectAlreadyEnrolledGuard(
        string $customerRepositoryId,
         string $projectId,
         string $subjectId
    ): void {
        $criteria = Criteria::create([
            'customerRepositoryId' => $customerRepositoryId,
            'projectId' => $projectId,
            'subjectId' => $subjectId,
        ]);

        if (!is_null($this->enrollmentsRepository->findOneByCriteria($criteria))) {
            throw new EnrollmentAlreadyExistsException();
        }
    }
}