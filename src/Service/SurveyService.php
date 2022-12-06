<?php declare(strict_types=1);

namespace App\Service;

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
}
