<?php declare(strict_types=1);

namespace App\Service;

use App\Contract\FixedRangeAnswerStatsDto;
use App\Contract\FreeTextAnswerStatsDto;
use App\Contract\QuestionDto;
use App\Contract\QuestionType;
use App\Contract\SubmittedSurveyDto;
use App\Contract\SurveyDto;
use App\Entity\Answer;
use App\Entity\Question;
use App\Repository\AnswerRepository;
use App\Repository\QuestionRepository;
use App\Repository\SurveyRepository;
use Doctrine\ORM\EntityManagerInterface;
use JetBrains\PhpStorm\ArrayShape;

class SurveyService
{
    public function __construct(
        private SurveyRepository $surveyRepository,
        private QuestionRepository $questionRepository,
        private AnswerRepository $answerRepository,
        private EntityManagerInterface $em,
    ) {
    }

    public function surveyToDto(int $id): ?SurveyDto
    {
        $survey = $this->surveyRepository->find($id);
        if (!$survey) {
            return null;
        }

        $questions = $this->questionRepository->findBy(['id' => $survey->getQuestionIds()]);
        $questions = array_map(fn(Question $q): QuestionDto => $q->toDto(), $questions);

        return new SurveyDto($id, $survey->getName(), $questions);
    }

    /**
     * @param SubmittedSurveyDto $input
     * @return void
     * @throws \Throwable
     */
    public function saveSubmittedSurvey(SubmittedSurveyDto $input): void
    {
        foreach ($input->getAnswers() as $a) {
            $question = $this->questionRepository->find($a->getQuestionId());

            if (!$question) {
//todo: log or throw
                continue;
            }
            $type = $question->getType();
            $value = $a->getValue();

            if ($type === QuestionType::SINGLE_05_RANGE) {
                $fixed = (int)$value;
                if ($fixed < 0 || $fixed > 5) {
                    throw new \LogicException(
                        "$value our of range for question #{$a->getQuestionId()} {$question->getName()}"
                    );
                }
                $text = null;
            } else {
                $fixed = null;
                $text = (string)$value;
            }

            $answer = new Answer($question, $fixed, $text);
            $this->em->persist($answer);
        }

        $this->em->flush();
    }

//todo: a couple more DTOs?

    /**
     * @return array<string, mixed>
     */
    #[ArrayShape(['fixed range questions' => "\App\Contract\FixedRangeAnswerStatsDto[]", 'free text questions' => "\App\Contract\FreeTextAnswerStatsDto"])]
    public function getAllStats(): array
    {
        return [
            'fixed range questions' => $this->rangeAnswersStats(),
            'free text questions' => $this->freeTextStats(),
        ];
    }

    /**
     * @return FixedRangeAnswerStatsDto[]
     */
    public function rangeAnswersStats(): array
    {
        $rangeQuestions = $this->questionRepository->findBy(['type' => QuestionType::SINGLE_05_RANGE]);

        $stats = [];
        foreach ($rangeQuestions as $q) {
            $count = $this->countAnswers([$q]);
            $distribution = $this->answerRepository->getDistribution($q);
            $average = $this->average($distribution);

            $stats[] = new FixedRangeAnswerStatsDto(
                questionId: $q->getId(),
                answers: $count,
                average: $average,
                distribution: $distribution
            );
        }

        return $stats;
    }

    public function freeTextStats(): FreeTextAnswerStatsDto
    {
        $freeTextQuestions = $this->questionRepository->findBy(['type' => QuestionType::FREE_TEXT]);

        $count = $this->countAnswers($freeTextQuestions);
        $frequency = $this->getWordFrequency($freeTextQuestions, 20);

        return new FreeTextAnswerStatsDto($count, $frequency);
    }

    /**
     * @param int[]|Question[] $questions
     * @param int $topWords
     * @return array<string, int>
     */
    private function getWordFrequency(array $questions, int $topWords): array
    {
        $frequency = [];
        foreach ($questions as $q) {
            $textIterable = $this->answerRepository->getText($q);
            foreach ($textIterable as $answer) {
                $this->updateWordFrequency($frequency, (string)$answer);
            }
        }

        // arrr!
        arsort($frequency, SORT_NUMERIC);
        return array_slice($frequency, 0, $topWords);
    }

    /**
     * @param int[]|Question[] $questions
     * @return int
     */
    private function countAnswers(array $questions): int
    {
        return $this->answerRepository->count(['question' => $questions]);
    }

    private function updateWordFrequency(array &$dictionary, string $text): void
    {
        // just English letters, spaces and dashes. barbaric!
        $text = preg_replace('/[^a-zA-Z -]+/', '', $text);

        $words = explode(' ', $text);
        foreach ($words as $word) {
            $word = trim($word);
            if (mb_strlen($word) < 2) { // and only 3+ lettered words!
                continue;
            }
            if (array_key_exists($word, $dictionary)) {
                $dictionary[$word]++;
            } else {
                $dictionary[$word] = 1;
            }
        }
    }

    /**
     * @param array<int, array<string, ?int>> $distribution
     * @return float
     */
    private function average(array $distribution): float
    {
        $n = 0;
        $acc = 0;
        foreach ($distribution as $_ => ['option' => $value, 'cnt' => $count]) {
            if ($value === null) { // skip unanswered
                continue;
            }
            $n += $count;
            $acc += $value * $count;
        }

        if ($n === 0) {
            return 0.0;
        }
        return $acc / $n;
    }
}
