<?php
declare(strict_types=1);

namespace Tests;

use App\Shared\Application\ResponsePayload;
use DI\Container;
use DI\ContainerBuilder;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\ClientException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase as PHPUnit_TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\StreamInterface;
use Slim\App;
use Slim\Factory\AppFactory;
use Slim\Psr7\Factory\StreamFactory;
use Slim\Psr7\Headers;
use Slim\Psr7\Request as SlimRequest;
use Slim\Psr7\Uri;

class TestCase extends PHPUnit_TestCase
{
    use ProphecyTrait;

    private ?App $app = null;
    private ?Container $container = null;

    private function getAppInstance(): App
    {
        // Instantiate PHP-DI ContainerBuilder
        $containerBuilder = new ContainerBuilder();

        // Container intentionally not compiled for tests.

        // Set up settings
        $settings = require __DIR__ . '/../app/settings.php';
        $settings($containerBuilder);

        // Set up dependencies
        $dependencies = require __DIR__ . '/../app/dependencies.php';
        $dependencies($containerBuilder);

        // Set up repositories
        $repositories = require __DIR__ . '/../app/repositories.php';
        $repositories($containerBuilder);

        // Build PHP-DI Container instance
        $container = $containerBuilder->build();

        // Instantiate the app
        AppFactory::setContainer($container);
        $app = AppFactory::create();

        // Register middleware
        $middleware = require __DIR__ . '/../app/middleware.php';
        $middleware($app);

        // Register routes
        $routes = require __DIR__ . '/../app/routes.php';
        $routes($app);

        return $app;
    }
    protected function getApp(): App
    {
        if (!$this->app instanceof App) {
            $this->app = $this->getAppInstance();
        }

        return $this->app;
    }

    protected function getContainer(): Container
    {
        $this->getApp();

        if (!$this->container instanceof Container) {
            $this->container = $this->app->getContainer();
        }

        return $this->container;
    }

    protected function createRequest(
        string $method,
        string $path,
        array $headers = ['HTTP_ACCEPT' => 'application/json'],
        array $cookies = [],
        array $serverParams = []
    ): Request {
        $uri = new Uri('', '', 80, $path);
        $handle = fopen('php://temp', 'w+');
        $stream = (new StreamFactory())->createStreamFromResource($handle);

        $h = new Headers();
        foreach ($headers as $name => $value) {
            $h->addHeader($name, $value);
        }

        return new SlimRequest($method, $uri, $h, $cookies, $serverParams, $stream);
    }

    protected function makeJsonRequest(
        string $path,
        array $headers,
        array $body
    ): ResponseInterface {
        $headers['Content-Type'] = 'application/json';

        return $this->makeRequest(
            'POST',
            $path,
            $headers,
            json_encode($body)
        );
    }

    protected function makeRequest(
        string $method,
        string $path,
        array $headers = [],
        string $body = ''
    ): ResponseInterface {
        $request = $this->createRequest(
            $method,
            $path,
            $headers
        );

        $request->getBody()->write($body);

        return $this->getApp()->handle($request);
    }

    /** Maybe should be in a trait */
    protected function mockDependency(string $name, MockObject $mock): void
    {
        $container = $this->getContainer();
        $container->set($name, $mock);
    }

    /** Maybe should be in a trait */
    protected function createHttpClientMock(
        array $withs,
        array $returns
    ): MockObject {
        $httpClient = $this->createMock(Client::class);
        $matcher = $this->exactly(count($withs));
        $httpClient
            ->expects($matcher)
            ->method('request')
            ->withConsecutive(...$withs)
            ->willReturnCallback(
                function () use ($matcher, $returns) {
                    $return = $returns[$matcher->getInvocationCount() - 1];
                    if ($return instanceof Exception) {
                        throw $return;
                    } else {
                        return $return;
                    }
                }
            );

        return $httpClient;
    }

    protected function assertResponsePayload(
        ResponsePayload $expected,
        ResponseInterface $response
    ): void {
        $this->assertEquals(
            json_encode($expected, JSON_PRETTY_PRINT),
            (string) $response->getBody()
        );
    }

    protected function clientException(
        int $statusCode,
        string $message = ''
    ): BadResponseException {
        return new ClientException(
            $message,
            $this->createMock(RequestInterface::class),
            $this->createResponseMock($statusCode)
        );
    }

    protected function createResponseMock(int $statusCode, string $body = ''): ResponseInterface
    {
        $response = $this->createMock(ResponseInterface::class);
        $response
            ->expects($this->any())
            ->method('getStatusCode')
            ->willReturn($statusCode);

        if (!empty($body)) {
            $stream = $this->createMock(StreamInterface::class);
            $stream
                ->expects($this->any())
                ->method('getContents')
                ->willReturn($body);

            $response
                ->expects($this->any())
                ->method('getBody')
                ->willReturn($stream);
        }

        return $response;
    }
}
