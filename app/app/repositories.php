<?php
declare(strict_types=1);

use App\Enrollment\Domain\EnrollmentsRepositoryInterface;
use App\Enrollment\Infrastructure\Persistence\HttpEnrollmentsRepository;
use App\Project\Domain\ProjectsRepositoryInterface;
use App\Project\Infrastructure\Persistence\HttpProjectsRepository;
use App\Subject\Domain\SubjectsRepositoryInterface;
use App\Subject\Infrastructure\Persistence\HttpSubjectsRepository;
use DI\ContainerBuilder;

return function (ContainerBuilder $containerBuilder) {
    $containerBuilder->addDefinitions([
        SubjectsRepositoryInterface::class => \DI\autowire(HttpSubjectsRepository::class),
        ProjectsRepositoryInterface::class => \DI\autowire(HttpProjectsRepository::class),
        EnrollmentsRepositoryInterface::class => \DI\autowire(HttpEnrollmentsRepository::class),
    ]);
};
