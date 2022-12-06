<?php

namespace App\Entity;

use App\Contract\QuestionDto;
use App\Contract\QuestionType;
use App\Repository\QuestionRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: QuestionRepository::class)]
class Question
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'string', length: 255)]
    private string $name;

    #[ORM\Column(type: 'string', length: 255)]
    private string $type;

//todo: type enum/dto
    public function __construct(string $name, QuestionType $type)
    {
        $this->name = $name;
        $this->type = $type->value;
    }

    public function toDto(): QuestionDto
    {
        return new QuestionDto($this->getId(), $this->getName(), $this->getType());
    }

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
        return QuestionType::from($this->type);
    }
}
