<?php

namespace App\Entity;

use App\Repository\AnswerRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AnswerRepository::class)]
class Answer
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\ManyToOne(targetEntity: Question::class)]
    #[ORM\JoinColumn(nullable: false)]
    private $question;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $fixedNumeric;

    #[ORM\Column(type: 'text', nullable: true)]
    private $freeText;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getQuestion(): ?Question
    {
        return $this->question;
    }

    public function setQuestion(?Question $question): self
    {
        $this->question = $question;

        return $this;
    }

    public function getFixedNumeric(): ?int
    {
        return $this->fixedNumeric;
    }

    public function setFixedNumeric(?int $fixedNumeric): self
    {
        $this->fixedNumeric = $fixedNumeric;

        return $this;
    }

    public function getFreeText(): ?string
    {
        return $this->freeText;
    }

    public function setFreeText(?string $freeText): self
    {
        $this->freeText = $freeText;

        return $this;
    }
}
