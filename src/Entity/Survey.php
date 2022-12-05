<?php

namespace App\Entity;

use App\Repository\SurveyRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SurveyRepository::class)]
class Survey
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'string', length: 255)]
    private string $name;

    /** @var int[] */
    #[ORM\Column(type: 'json')]
    private array $questions;

    /**
     * @param Question[] $questions
     */
    public function __construct(string $name, array $questions)
    {
        $this->name = $name;
        $this->questions = array_map(fn(Question $x): int => $x->getId(), $questions);
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return int[]
     */
    public function getQuestionIds(): array
    {
        return $this->questions;
    }
}
