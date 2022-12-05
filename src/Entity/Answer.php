<?php

namespace App\Entity;

use App\Repository\AnswerRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AnswerRepository::class)]
#[ORM\Index(fields: ['fixedNumeric'], name: 'IDX_fixed')]
class Answer
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\ManyToOne(targetEntity: Question::class)]
    #[ORM\JoinColumn(nullable: false)]
    private Question $question;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $fixedNumeric;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $freeText;

    public function __construct(Question $question, ?int $fixedNumeric, ?string $freeText)
    {
        $this->question = $question;
        $this->fixedNumeric = $fixedNumeric;
        $this->freeText = $freeText;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getQuestion(): Question
    {
        return $this->question;
    }

    public function getFixedNumeric(): ?int
    {
        return $this->fixedNumeric;
    }

    public function getFreeText(): ?string
    {
        return $this->freeText;
    }
}
