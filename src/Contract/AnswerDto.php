<?php declare(strict_types=1);

namespace App\Contract;

class AnswerDto
{
    public function __construct(
        private int $questionId,
        private mixed $value,
    ) { }

    public function getQuestionId(): int
    {
        return $this->questionId;
    }

    public function getValue(): mixed
    {
        return $this->value;
    }
}
