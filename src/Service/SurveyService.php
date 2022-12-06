<?php declare(strict_types=1);

namespace App\Service;

use App\Contract\QuestionDto;
use App\Contract\QuestionType;
use App\Contract\SubmittedSurveyDto;
use App\Contract\SurveyDto;
use App\Entity\Answer;
use App\Entity\Question;
use App\Repository\QuestionRepository;
use App\Repository\SurveyRepository;
use Doctrine\ORM\EntityManagerInterface;

class SurveyService
{
    public function __construct(
        private SurveyRepository $surveyRepository,
        private QuestionRepository $questionRepository,
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
}
