<?php
declare(strict_types=1);

use App\Application\Actions\User\ListUsersAction;
use App\Application\Actions\User\ViewUserAction;
use App\Enrollment\Infrastructure\Http\EnrollSubjectInToProjectController;
use App\Subject\Infrastructure\Http\CreateSubjectController;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;

return function (App $app) {
    $app->options('/{routes:.*}', function (Request $request, Response $response) {
        // CORS Pre-Flight OPTIONS Request Handler
        return $response;
    });

    $app->group('/subjects', function (Group $group) {
        $group->post('', CreateSubjectController::class);
    });

    $app->group('/enrollments', function (Group $group) {
        $group->post('', EnrollSubjectInToProjectController::class);
    });
};
