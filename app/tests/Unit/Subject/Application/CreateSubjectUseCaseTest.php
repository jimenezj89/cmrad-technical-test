<?php
declare(strict_types=1);

use App\CustomerRepository\Domain\CustomerRepository;
use App\Shared\Domain\Criteria;
use App\Subject\Application\CreateSubjectDTO;
use App\Subject\Application\CreateSubjectUseCase;
use App\Subject\Domain\Subject;
use App\Subject\Domain\SubjectAlreadyExistsException;
use App\Subject\Domain\SubjectsRepositoryInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class CreateSubjectUseCaseTest extends TestCase
{
    private MockObject $subjectsRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->subjectsRepository = $this->createMock(SubjectsRepositoryInterface::class);
    }

    public function createSut(): CreateSubjectUseCase
    {
        return new CreateSubjectUseCase(
            $this->subjectsRepository
        );
    }

    public function testCreateSubjectUseCase(): void
    {
        $this->subjectsRepository
            ->expects($this->once())
            ->method('findOneByCriteria')
            ->with(
                Criteria::create([
                    'customerRepositoryId' => self::data()['customerRepositoryId'],
                    'subjectId' => self::data()['subjectId'],
                ])
            )->willReturn(null);

        $expectedSubject = self::subject();
        $this->subjectsRepository
            ->expects($this->once())
            ->method('create')
            ->with(
                $this->callback(
                    function (Subject $subject) use ($expectedSubject) {
                        $this->assertEquals($expectedSubject->getId(), $subject->getId());
                        $this->assertEquals($expectedSubject->getName(), $subject->getName());
                        $this->assertEquals($expectedSubject->getAge(), $subject->getAge());
                        $this->assertEquals($expectedSubject->getHeight(), $subject->getHeight());
                        $this->assertEquals($expectedSubject->getWeight(), $subject->getWeight());

                        return true;
                    }
                )
            );

        $subject = $this->createSut()->__invoke(self::dto());

        $this->assertEquals($expectedSubject->getId(), $subject->getId());
        $this->assertEquals($expectedSubject->getName(), $subject->getName());
        $this->assertEquals($expectedSubject->getAge(), $subject->getAge());
        $this->assertEquals($expectedSubject->getHeight(), $subject->getHeight());
        $this->assertEquals($expectedSubject->getWeight(), $subject->getWeight());
    }

    public function testExceptionIsThrownIfSubjectAlreadyExists(): void
    {
        $this->subjectsRepository
            ->expects($this->once())
            ->method('findOneByCriteria')
            ->with(
                Criteria::create([
                    'customerRepositoryId' => self::data()['customerRepositoryId'],
                    'subjectId' => self::data()['subjectId'],
                ])
            )->willReturn(self::subject());

        $this->expectException(SubjectAlreadyExistsException::class);
        $this->expectExceptionMessage('Subject already exists.');

        $this->createSut()->__invoke(self::dto());
    }

    private static function data(): array
    {
        return [
            'customerRepositoryId' => 'a-repository-id',
            'subjectId' => 'a-subject-id',
            'subjectName' => 'A subject name',
            'subjectAge' => 32,
            'subjectHeight' => 185,
            'subjectWeight' => 95,
        ];
    }

    private static function dto(): CreateSubjectDTO
    {
        return CreateSubjectDTO::create(
            self::data()['customerRepositoryId'],
            self::data()['subjectId'],
            self::data()['subjectName'],
            self::data()['subjectAge'],
            self::data()['subjectHeight'],
            self::data()['subjectWeight'],
        );
    }

    private static function subject(): Subject
    {
        return Subject::create(
            self::data()['subjectId'],
            self::data()['subjectName'],
            self::data()['subjectAge'],
            self::data()['subjectHeight'],
            self::data()['subjectWeight'],
            CustomerRepository::create(
                self::data()['customerRepositoryId'],
            )
        );

    }
}