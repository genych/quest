<?php declare(strict_types=1);

namespace App\Contract;

class FreeTextAnswerStatsDto
{
    /**
     * @param int $answers
     * @param array<string, int> $topWords
     */
    public function __construct(
        private int $answers,
        private array $topWords,
    ) { }

    public function getAnswers(): int
    {
        return $this->answers;
    }

    /**
     * @return array<string, int>
     */
    public function getTopWords(): array
    {
        return $this->topWords;
    }
}
