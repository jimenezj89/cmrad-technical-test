<?php
declare(strict_types=1);

namespace App\Enrollment\Infrastructure\Persistence;

use App\CustomerRepository\Domain\CustomerRepository;
use App\Enrollment\Domain\Enrollment;
use App\Enrollment\Domain\EnrollmentsRepositoryInterface;
use App\Project\Domain\Project;
use App\Shared\Domain\Criteria;
use App\Shared\Infrastructure\Persistence\HttpBaseRepository;
use App\Subject\Domain\Subject;
use Fig\Http\Message\StatusCodeInterface;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\ClientException;

final class HttpEnrollmentsRepository extends HttpBaseRepository implements EnrollmentsRepositoryInterface
{
    /**
     * Any other exceptions should reach the error handler because, after input
     * filtering/validation is implemented, any error should be an implementation
     * error.
     */
    public function findOneByCriteria(Criteria $criteria): ?Enrollment
    {
        $customerRepositoryId = $criteria->filters()['customerRepositoryId'];
        $projectId = $criteria->filters()['projectId'];
        $subjectId = $criteria->filters()['subjectId'];

        try {
            $response = $this->request(
                self::GET,
                "/repositories/$customerRepositoryId/projects/$projectId/subjects/$subjectId"
            );

            $body = json_decode(
                $response->getBody()->getContents(),
                true
            );

            /**
             * /!\ I'm not happy with this
             */
            $customerRepository = CustomerRepository::create($customerRepositoryId);
            return Enrollment::create(
                Project::create($projectId),
                Subject::create(
                    $body['subject']['id'],
                    $body['subject']['name'],
                    $body['subject']['age'],
                    $body['subject']['height'],
                    $body['subject']['weight'],
                    $customerRepository
                ),
                $body['rol']
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
     * Any other exceptions should reach the error handler because, after input
     * filtering/validation is implemented, any error should be an implementation
     * error.
     */
    public function create(Enrollment $enrollment): void
    {
        $customerRepositoryId = $enrollment
            ->getSubject()
            ->getCustomerRepository()
            ->getId();
        $projectId = $enrollment->getProject()->getId();

        $this->request(
            self::POST,
            "/repositories/$customerRepositoryId/projects/$projectId/subjects",
            [
                'json' => array_merge(
                    $enrollment->getSubject()->jsonSerialize(),
                    ['rol' => $enrollment->getRol()]
                ),
            ],
        );
    }
}