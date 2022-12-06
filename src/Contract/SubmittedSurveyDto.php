<?php declare(strict_types=1);

namespace App\Contract;

class SubmittedSurveyDto
{
    /**
     * @param int $surveyId
     * @param AnswerDto[] $answers
     */
    public function __construct(
        private int $surveyId,
        private array $answers,
    ) { }

    public function getSurveyId(): int
    {
        return $this->surveyId;
    }

    /**
     * @return AnswerDto[]
     */
    public function getAnswers(): array
    {
        return $this->answers;
    }
}
