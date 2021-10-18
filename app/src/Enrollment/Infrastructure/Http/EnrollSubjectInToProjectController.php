<?php
declare(strict_types=1);

namespace App\Enrollment\Infrastructure\Http;

use App\Enrollment\Application\EnrollSubjectInToProjectDTO;
use App\Enrollment\Application\EnrollSubjectInToProjectUseCase;
use App\Enrollment\Domain\EnrollmentAlreadyExistsException;
use App\Project\Domain\ProjectNotFoundException;
use App\Shared\Application\ResponseError;
use App\Shared\Application\ResponsePayload;
use App\Shared\Infrastructure\Http\BaseController;
use App\Subject\Domain\SubjectNotFoundException;
use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface;

final class EnrollSubjectInToProjectController extends BaseController
{
    private EnrollSubjectInToProjectUseCase $enrollSubjectInToProjectUseCase;

    public function __construct(
        EnrollSubjectInToProjectUseCase $enrollSubjectInToProjectUseCase
    ) {
        $this->enrollSubjectInToProjectUseCase = $enrollSubjectInToProjectUseCase;
    }

    protected function execute(): ResponseInterface
    {
        $body = (array) $this->request->getParsedBody();

        try {
            $enrollment = $this->enrollSubjectInToProjectUseCase->__invoke(
                EnrollSubjectInToProjectDTO::create(
                    $this->request->getHeaderLine('X-customer-id'),
                    $body['projectId'],
                    $body['subjectId'],
                    $body['rol'],
                )
            );

            return $this->respond(
                ResponsePayload::create(
                    StatusCodeInterface::STATUS_CREATED,
                    $enrollment
                )
            );
        } catch (ProjectNotFoundException |
            SubjectNotFoundException |
            EnrollmentAlreadyExistsException $e
        ) {
            return $this->respond(
                ResponsePayload::create(
                    StatusCodeInterface::STATUS_BAD_REQUEST,
                    null,
                    ResponseError::create($e->getMessage())
                )
            );
        }
    }
}