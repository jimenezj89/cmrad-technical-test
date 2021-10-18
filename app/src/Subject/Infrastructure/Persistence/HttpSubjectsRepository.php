<?php

namespace App\Subject\Infrastructure\Persistence;

use App\CustomerRepository\Domain\CustomerRepository;
use App\Shared\Domain\Criteria;
use App\Shared\Infrastructure\Persistence\HttpBaseRepository;
use App\Subject\Domain\Subject;
use App\Subject\Domain\SubjectsRepositoryInterface;
use Fig\Http\Message\StatusCodeInterface;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\ClientException;

final class HttpSubjectsRepository extends HttpBaseRepository implements SubjectsRepositoryInterface
{
    /**
     * Any other exceptions should reach the error handler because, after input
     * filtering/validation is implemented, any error should be an implementation
     * error.
     */
    public function findOneByCriteria(Criteria $criteria): ?Subject
    {
        $customerRepositoryId = $criteria->filters()['customerRepositoryId'];
        $subjectId = $criteria->filters()['subjectId'];

        try {
            $response = $this->request(
                self::GET,
                "/repositories/$customerRepositoryId/subjects/$subjectId"
            );

            $body = json_decode(
                $response->getBody()->getContents(),
                true
            );

            return Subject::create(
                $body['id'],
                $body['name'],
                $body['age'],
                $body['height'],
                $body['weight'],
                CustomerRepository::create($body['repository']['id'])
            );
        } catch (BadResponseException $e) {
            if ($e instanceof ClientException &&
                $e->getResponse()->getStatusCode() === StatusCodeInterface::STATUS_NOT_FOUND
            ) {
                return null;
            }

            throw $e;
        }
    }

    /**
     * Any exception should reach the error handler because, after input
     * filtering/validation is implemented, any error should be an implementation
     * error.
     *
     * Returning subject data could be an improvement if core api does.
     */
    public function create(Subject $subject): void
    {
        $this->request(
            self::POST,
            "/repositories/{$subject->getCustomerRepository()->getId()}/subjects",
            [
                'json' => [
                    'id' => $subject->getId(),
                    'name' => $subject->getName(),
                    'age' => $subject->getAge(),
                    'height' => $subject->getHeight(),
                    'weight' => $subject->getWeight(),
                ],
            ]
        );
    }
}