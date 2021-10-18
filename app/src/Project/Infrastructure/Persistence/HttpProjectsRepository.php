<?php
declare(strict_types=1);

namespace App\Project\Infrastructure\Persistence;

use App\Project\Domain\Project;
use App\Project\Domain\ProjectsRepositoryInterface;
use App\Shared\Domain\Criteria;
use App\Shared\Infrastructure\Persistence\HttpBaseRepository;
use Fig\Http\Message\StatusCodeInterface;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\ClientException;

final class HttpProjectsRepository extends HttpBaseRepository implements ProjectsRepositoryInterface
{
    /**
     * Any other exceptions should reach the error handler because, after input
     * filtering/validation is implemented, any error should be an implementation
     * error.
     */
    public function findOneByCriteria(Criteria $criteria): ?Project
    {
        $customerRepositoryId = $criteria->filters()['customerRepositoryId'];
        $projectId = $criteria->filters()['projectId'];

        try {
            $response = $this->request(
                self::GET,
                "/repositories/$customerRepositoryId/projects/$projectId"
            );

            $body = json_decode(
                $response->getBody()->getContents(),
                true
            );

            return Project::create(
                $body['id']
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
}