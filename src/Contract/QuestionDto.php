<?php declare(strict_types=1);

namespace App\Contract;

class QuestionDto
{
    public function __construct(
        private int $id,
        private string $name,
        private QuestionType $type,
    ) { }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getType(): QuestionType
    {
        return $this->type;
    }
}
