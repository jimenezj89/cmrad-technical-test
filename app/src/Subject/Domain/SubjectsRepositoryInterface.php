<?php

namespace App\Subject\Domain;

use App\Shared\Domain\Criteria;

interface SubjectsRepositoryInterface
{
    public function findOneByCriteria(Criteria $criteria): ?Subject;
    public function create(Subject $subject): void;
}