<?php declare(strict_types=1);

namespace App\Tests\Service;

use App\Contract\QuestionType;
use App\Entity\Answer;
use App\Entity\Question;
use App\Repository\AnswerRepository;
use App\Repository\QuestionRepository;
use App\Repository\SurveyRepository;
use App\Service\SurveyService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class SurveyServiceTest extends TestCase
{
    private Question $question;

    /** @var Answer[] */
    private array $answers;

    public function setUp(): void
    {
        // be careful, they don't have id. use reflection to fake them
        $this->question = new Question('free', QuestionType::FREE_TEXT);
        $this->answers = [
            new Answer($this->question, null, 'some text'),
            new Answer($this->question, null, 'another text comment'),
        ];
    }

    /**
     * not exhaustive and quite brittle. just example how to test service methods without DB.
     */
    public function testWordFrequency(): void
    {
        // stub everything DB-related
        $em = $this->createStub(EntityManagerInterface::class);
        $surveyRepo = $this->createStub(SurveyRepository::class);

        $questionRepo = $this->createStub(QuestionRepository::class);
        $questionRepo->method('findBy')->willReturn([$this->question]);

        $answerRepo = $this->createStub(AnswerRepository::class);
        $answerRepo->method('getText')->willReturnCallback($this->fakeText(...));
        $answerRepo->method('count')->willReturnCallback(fn($_): int => count($this->answers));

        // but service is real
        $service = new SurveyService($surveyRepo, $questionRepo, $answerRepo, $em);

        $stats = $service->freeTextStats();
        $words = $stats->getTopWords();

        // not good assertions because it doubles StatsTest
        self::assertEquals(2, $stats->getAnswers());
        self::assertArrayHasKey('text', $words);
        self::assertEquals(2, $words['text']);
    }

    /**
     * @param Question $question
     * @return iterable<?string>
     */
    private function fakeText(Question $question): iterable
    {
        foreach ($this->answers as $answer) {
            yield $answer->getFreeText();
        }
    }
}
