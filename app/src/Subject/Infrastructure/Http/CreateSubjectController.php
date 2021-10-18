<?php

namespace App\Subject\Infrastructure\Http;


use App\Shared\Application\ResponseError;
use App\Shared\Application\ResponsePayload;
use App\Shared\Infrastructure\Http\BaseController;
use App\Subject\Application\CreateSubjectDTO;
use App\Subject\Application\CreateSubjectUseCase;
use App\Subject\Domain\SubjectAlreadyExistsException;
use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

class CreateSubjectController extends BaseController
{
    private CreateSubjectUseCase $createSubjectUseCase;

    public function __construct(
        CreateSubjectUseCase $createSubjectUseCase
    ) {
        $this->createSubjectUseCase = $createSubjectUseCase;
    }

    protected function execute(): ResponseInterface
    {
        $body = (array) $this->request->getParsedBody();

        try {
            $subject = $this->createSubjectUseCase->__invoke(
                CreateSubjectDTO::create(
                    $this->request->getHeaderLine('X-customer-id'),
                    $body['id'],
                    $body['name'],
                    $body['age'],
                    $body['height'],
                    $body['weight'],
                )
            );

            return $this->respond(
                ResponsePayload::create(
                    StatusCodeInterface::STATUS_CREATED,
                    $subject
                )
            );
        } catch (SubjectAlreadyExistsException $e) {
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