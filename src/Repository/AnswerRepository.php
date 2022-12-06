<?php

namespace App\Repository;

use App\Entity\Answer;
use App\Entity\Question;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Answer>
 *
 * @method Answer|null find($id, $lockMode = null, $lockVersion = null)
 * @method Answer|null findOneBy(array $criteria, array $orderBy = null)
 * @method Answer[]    findAll()
 * @method Answer[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AnswerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Answer::class);
    }

    /**
     * @param Question|int $question
     * @return iterable<?string>
     */
    public function getText(Question|int $question): iterable
    {
        $dql = 'select a.freeText from App\Entity\Answer a where a.question = :question';

        $chunks = $this->getEntityManager()->createQuery($dql)
            ->setParameter('question', $question)
            ->toIterable();

        foreach ($chunks as $chunk) { // unwrap results
            yield $chunk['freeText'];
        }
    }
}
