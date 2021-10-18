<?php
declare(strict_types=1);

use App\Shared\Infrastructure\Slim\Settings\Settings;
use App\Shared\Infrastructure\Slim\Settings\SettingsInterface;
use DI\ContainerBuilder;
use Monolog\Logger;

return function (ContainerBuilder $containerBuilder) {

    // Global Settings Object
    $containerBuilder->addDefinitions([
        SettingsInterface::class => function () {
            return new Settings([
                'displayErrorDetails' => true, // Should be set to false in production
                'logError'            => false,
                'logErrorDetails'     => false,
                'logger' => [
                    'name' => 'CMRAD-STAGING-API',
                    'path' => '/var/log/app.log',
                    'level' => Logger::DEBUG,
                ],
            ]);
        }
    ]);
};
