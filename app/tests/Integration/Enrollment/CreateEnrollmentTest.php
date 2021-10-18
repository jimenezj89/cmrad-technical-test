<?php
declare(strict_types=1);

namespace Tests\Integration\Enrollment;

use App\Shared\Application\ResponseError;
use App\Shared\Application\ResponsePayload;
use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Client\ClientInterface;
use Tests\TestCase;

final class CreateEnrollmentTest extends TestCase
{
    public function testCreateEnrollment(): void
    {
        $customerRepositoryId = 'a-customer-repository-id';
        $projectId = 'a-project-id';
        $subjectId = 'a-subject-id';

        $httpClient = $this->createHttpClientMock(
            [
                [
                    'GET',
                    "/repositories/$customerRepositoryId/projects/$projectId",
                ],
                [
                    'GET',
                    "/repositories/$customerRepositoryId/subjects/$subjectId",
                ],
                [
                    'GET',
                    "/repositories/$customerRepositoryId/projects/$projectId/subjects/$subjectId"
                ],
                [
                    'POST',
                    "/repositories/$customerRepositoryId/projects/$projectId/subjects"
                ],
            ],
            [
                $this->createResponseMock(
                    StatusCodeInterface::STATUS_OK,
                    json_encode([
                        'id' => $projectId,
                    ])
                ),
                $this->createResponseMock(
                    StatusCodeInterface::STATUS_OK,
                    json_encode([
                        'id' => $subjectId,
                        'name' => 'A subject name',
                        'age' => 32,
                        'height' => 185,
                        'weight' => 95,
                        'repository' => [
                            'id' => $customerRepositoryId,
                        ],
                    ])
                ),
                $this->clientException(StatusCodeInterface::STATUS_NOT_FOUND),
                $this->createResponseMock(StatusCodeInterface::STATUS_CREATED),
            ]
        );

        $this->mockDependency(ClientInterface::class, $httpClient);

        $response = $this->makeJsonRequest(
            '/enrollments',
            [
                'X-customer-id' => $customerRepositoryId,
            ],
            [
                'projectId' => $projectId,
                'subjectId' => $subjectId,
                'rol' => 'control',
            ]
        );

        $this->assertEquals(
            StatusCodeInterface::STATUS_CREATED,
            $response->getStatusCode()
        );

        /**
         * TODO: Check response body
         *
         * ...
         */
    }

    public function testBadResponseIsReturnedIfProjectDoesNotExists(): void
    {
        $customerRepositoryId = 'a-customer-repository-id';
        $projectId = 'a-project-id';
        $subjectId = 'a-subject-id';

        $httpClient = $this->createHttpClientMock(
            [
                [
                    'GET',
                    "/repositories/$customerRepositoryId/projects/$projectId",
                ],
            ],
            [
                $this->clientException(StatusCodeInterface::STATUS_NOT_FOUND),
            ]
        );

        $this->mockDependency(ClientInterface::class, $httpClient);

        $response = $this->makeJsonRequest(
            '/enrollments',
            [
                'X-customer-id' => $customerRepositoryId,
            ],
            [
                'projectId' => $projectId,
                'subjectId' => $subjectId,
                'rol' => 'control',
            ]
        );

        $expectedResponsePayload = ResponsePayload::create(
            StatusCodeInterface::STATUS_BAD_REQUEST,
            null,
            ResponseError::create('Project does not exists.')
        );
        $this->assertEquals(
            $expectedResponsePayload->getStatusCode(),
            $response->getStatusCode()
        );
        $this->assertResponsePayload($expectedResponsePayload, $response);
    }

    public function testBadResponseIsReturnedIfSubjectDoesNotExists(): void
    {
        $customerRepositoryId = 'a-customer-repository-id';
        $projectId = 'a-project-id';
        $subjectId = 'a-subject-id';

        $httpClient = $this->createHttpClientMock(
            [
                [
                    'GET',
                    "/repositories/$customerRepositoryId/projects/$projectId",
                ],
                [
                    'GET',
                    "/repositories/$customerRepositoryId/subjects/$subjectId",
                ],
            ],
            [
                $this->createResponseMock(
                    StatusCodeInterface::STATUS_OK,
                    json_encode([
                        'id' => $projectId,
                    ])
                ),
                $this->clientException(StatusCodeInterface::STATUS_NOT_FOUND),
            ]
        );

        $this->mockDependency(ClientInterface::class, $httpClient);

        $response = $this->makeJsonRequest(
            '/enrollments',
            [
                'X-customer-id' => $customerRepositoryId,
            ],
            [
                'projectId' => $projectId,
                'subjectId' => $subjectId,
                'rol' => 'control',
            ]
        );

        $expectedResponsePayload = ResponsePayload::create(
            StatusCodeInterface::STATUS_BAD_REQUEST,
            null,
            ResponseError::create('Subject does not exists.')
        );
        $this->assertEquals(
            $expectedResponsePayload->getStatusCode(),
            $response->getStatusCode()
        );
        $this->assertResponsePayload($expectedResponsePayload, $response);
    }

    public function testBadResponseIsReturnedIfSubjectIsAlreadyEnrolled(): void
    {
        $customerRepositoryId = 'a-customer-repository-id';
        $projectId = 'a-project-id';
        $subjectId = 'a-subject-id';
        $projectResponseBody = [
            'id' => $projectId,
        ];
        $subjectResponseBody = [
            'id' => $subjectId,
            'name' => 'A subject name',
            'age' => 32,
            'height' => 185,
            'weight' => 95,
            'repository' => [
                'id' => $customerRepositoryId,
            ],
        ];

        $httpClient = $this->createHttpClientMock(
            [
                [
                    'GET',
                    "/repositories/$customerRepositoryId/projects/$projectId",
                ],
                [
                    'GET',
                    "/repositories/$customerRepositoryId/subjects/$subjectId",
                ],
                [
                    'GET',
                    "/repositories/$customerRepositoryId/projects/$projectId/subjects/$subjectId"
                ],
            ],
            [
                $this->createResponseMock(
                    StatusCodeInterface::STATUS_OK,
                    json_encode($projectResponseBody)
                ),
                $this->createResponseMock(
                    StatusCodeInterface::STATUS_OK,
                    json_encode($subjectResponseBody)
                ),
                $this->createResponseMock(
                    StatusCodeInterface::STATUS_OK,
                    json_encode([
                        'project' => $projectResponseBody,
                        'subject' => $subjectResponseBody,
                        'rol' => 'control',
                    ])
                ),
            ]
        );

        $this->mockDependency(ClientInterface::class, $httpClient);

        $response = $this->makeJsonRequest(
            '/enrollments',
            [
                'X-customer-id' => $customerRepositoryId,
            ],
            [
                'projectId' => $projectId,
                'subjectId' => $subjectId,
                'rol' => 'control',
            ]
        );

        $expectedResponsePayload = ResponsePayload::create(
            StatusCodeInterface::STATUS_BAD_REQUEST,
            null,
            ResponseError::create('Subject is already enrolled')
        );
        $this->assertEquals(
            $expectedResponsePayload->getStatusCode(),
            $response->getStatusCode()
        );
        $this->assertResponsePayload($expectedResponsePayload, $response);
    }
}