<?php declare(strict_types=1);

namespace App\Contract;

class SurveyDto
{
    /**
     * @param int $id
     * @param QuestionDto[] $questions
     */
    public function __construct(
        private int $id,
        private string $name,
        private array $questions,
    ) { }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return QuestionDto[]
     */
    public function getQuestions(): array
    {
        return $this->questions;
    }
}
