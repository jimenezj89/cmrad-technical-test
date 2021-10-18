<?php

namespace App\Subject\Application;

use App\CustomerRepository\Domain\CustomerRepository;
use App\Shared\Domain\Criteria;
use App\Subject\Domain\Subject;
use App\Subject\Domain\SubjectAlreadyExistsException;
use App\Subject\Domain\SubjectsRepositoryInterface;

final class CreateSubjectUseCase
{
    private SubjectsRepositoryInterface $subjectsRepository;

    public function __construct(
        SubjectsRepositoryInterface $subjectsRepository
    ) {
        $this->subjectsRepository = $subjectsRepository;
    }

    /**
     * @throws SubjectAlreadyExistsException
     */
    public function __invoke(CreateSubjectDTO $dto): Subject
    {
        $this->subjectExistsGuard(
            $dto->customerRepositoryId(),
            $dto->subjectId()
        );

        $subject = Subject::create(
            $dto->subjectId(),
            $dto->subjectName(),
            $dto->subjectAge(),
            $dto->subjectHeight(),
            $dto->subjectWeight(),
            CustomerRepository::create($dto->customerRepositoryId())
        );

        $this->subjectsRepository->create($subject);

        return $subject;
    }

    /**
     * @throws SubjectAlreadyExistsException
     */
    private function subjectExistsGuard(
        string $customerRepositoryId,
        string $subjectId
    ): void {
        $criteria = Criteria::create([
            'customerRepositoryId' => $customerRepositoryId,
            'subjectId' => $subjectId,
        ]);

        if (!is_null($this->subjectsRepository->findOneByCriteria($criteria))) {
            throw new SubjectAlreadyExistsException();
        }
    }
}