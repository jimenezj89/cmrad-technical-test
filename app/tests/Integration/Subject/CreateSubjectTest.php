<?php
declare(strict_types=1);

namespace Tests\Integration\Subject;

use App\CustomerRepository\Domain\CustomerRepository;
use App\Shared\Application\ResponseError;
use App\Shared\Application\ResponsePayload;
use App\Subject\Domain\Subject;
use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\ResponseInterface;
use Tests\TestCase;

final class CreateSubjectTest extends TestCase
{
    public function testCreateSubject(): void
    {
        $customerRepositoryId = 'a-customer-repository-id';
        $subjectId = 'a-subject-id';
        $subjectName = 'A subject Name';
        $subjectAge = 32;
        $subjectHeight = 182;
        $subjectWeight = 95;

        $httpClient = $this->createHttpClientMock(
            [
                [
                    'GET',
                    "/repositories/$customerRepositoryId/subjects/$subjectId",
                ],
                [
                    'POST',
                    "/repositories/$customerRepositoryId/subjects",
                    [
                        'json' => [
                            'id' => $subjectId,
                            'name' => $subjectName,
                            'age' => $subjectAge,
                            'height' => $subjectHeight,
                            'weight' => $subjectWeight,
                        ]
                    ]
                ],
            ],
            [
                $this->clientException(StatusCodeInterface::STATUS_NOT_FOUND),
                $this->createMock(ResponseInterface::class),
            ]
        );

        $this->mockDependency(ClientInterface::class, $httpClient);

        $response = $this->makeJsonRequest(
            '/subjects',
            [
                'X-customer-id' => $customerRepositoryId,
            ],
            [
                'id' => $subjectId,
                'name' => $subjectName,
                'age' => $subjectAge,
                'height' => $subjectHeight,
                'weight' => $subjectWeight,
            ]
        );

        $this->assertEquals(
            StatusCodeInterface::STATUS_CREATED,
            $response->getStatusCode()
        );

        $subject = Subject::create(
            $subjectId,
            $subjectName,
            $subjectAge,
            $subjectHeight,
            $subjectWeight,
            CustomerRepository::create($customerRepositoryId)
        );
        $expectedResponsePayload = ResponsePayload::create(
            StatusCodeInterface::STATUS_CREATED,
            $subject
        );

        $this->assertEquals(
            $expectedResponsePayload->getStatusCode(),
            $response->getStatusCode()
        );
        $this->assertResponsePayload($expectedResponsePayload, $response);
    }

    public function testBadResponseIsReturnedIfSubjectAlreadyExists(): void
    {
        $customerRepositoryId = 'a-customer-repository-id';
        $subjectId = 'a-subject-id';
        $subjectName = 'A subject Name';
        $subjectAge = 32;
        $subjectHeight = 182;
        $subjectWeight = 95;

        $httpClient = $this->createHttpClientMock(
            [
                [
                    'GET',
                    "/repositories/$customerRepositoryId/subjects/$subjectId",
                ],
            ],
            [
                $this->createResponseMock(
                    StatusCodeInterface::STATUS_OK,
                    json_encode([
                        'id' => $subjectId,
                        'name' => $subjectName,
                        'age' => $subjectAge,
                        'height' => $subjectHeight,
                        'weight' => $subjectWeight,
                        'repository' => [
                            'id' => $customerRepositoryId,
                        ],
                    ])
                ),
            ]
        );

        $this->mockDependency(ClientInterface::class, $httpClient);

        $response = $this->makeJsonRequest(
            '/subjects',
            [
                'X-customer-id' => $customerRepositoryId,
            ],
            [
                'id' => $subjectId,
                'name' => $subjectName,
                'age' => $subjectAge,
                'height' => $subjectHeight,
                'weight' => $subjectWeight,
            ]
        );

        $expectedResponsePayload = ResponsePayload::create(
            StatusCodeInterface::STATUS_BAD_REQUEST,
            null,
            ResponseError::create('Subject already exists.')
        );
        $this->assertEquals(
            $expectedResponsePayload->getStatusCode(),
            $response->getStatusCode()
        );
        $this->assertResponsePayload($expectedResponsePayload, $response);
    }
}