<?php declare(strict_types=1);

namespace App\Contract;

class FixedRangeAnswerStatsDto
{
    /**
     * @param int $questionId
     * @param int $answers
     * @param float $average
     * @param array<int, array<string, ?int>> $distribution
     */
    public function __construct(
        private int $questionId,
        private int $answers,
        private float $average,
        private array $distribution,
    ) { }

    public function getQuestionId(): int
    {
        return $this->questionId;
    }

    public function getAnswers(): int
    {
        return $this->answers;
    }

    public function getAverage(): float
    {
        return $this->average;
    }

    /**
     * @return array<int, array<string, ?int>>
     */
    public function getDistribution(): array
    {
        return $this->distribution;
    }
}
