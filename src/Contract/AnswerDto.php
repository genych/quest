<?php declare(strict_types=1);

namespace App\Contract;

class AnswerDto
{
    public function __construct(
        private int $questionId,
        private int|string|null $value,
    ) { }

    public function getQuestionId(): int
    {
        return $this->questionId;
    }

    public function getValue(): int|string|null
    {
        return $this->value;
    }
}
