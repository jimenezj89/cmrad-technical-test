<?php
declare(strict_types=1);

use App\Enrollment\Infrastructure\Http\EnrollSubjectInToProjectController;
use App\Subject\Infrastructure\Http\CreateSubjectController;
use DI\ContainerBuilder;

return function (ContainerBuilder $containerBuilder) {
    $containerBuilder->addDefinitions([
        CreateSubjectController::class => \DI\autowire(CreateSubjectController::class),
        EnrollSubjectInToProjectController::class => \DI\autowire(EnrollSubjectInToProjectController::class),
    ]);
};
