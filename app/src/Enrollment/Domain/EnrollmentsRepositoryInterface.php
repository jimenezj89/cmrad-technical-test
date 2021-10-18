<?php
declare(strict_types=1);

namespace App\Enrollment\Domain;

use App\Shared\Domain\Criteria;

interface EnrollmentsRepositoryInterface
{
    public function findOneByCriteria(Criteria $criteria): ?Enrollment;
    public function create(Enrollment $enrollment): void;
}