<?php

namespace App\Project\Domain;

use App\Shared\Domain\Criteria;

interface ProjectsRepositoryInterface
{
    public function findOneByCriteria(Criteria $criteria): ?Project;
}