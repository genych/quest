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

//todo: shape? dto?
    /**
     * @param Question|int $question
     * @return mixed[] [["option": ?int, "cnt": int]]
     */
    public function getDistribution(Question|int $question): array
    {
        $dql = 'select a.fixedNumeric option, count(1) cnt from App\Entity\Answer a 
            where a.question = :question group by a.fixedNumeric order by a.fixedNumeric ASC';

        return $this->getEntityManager()->createQuery($dql)
            ->setParameter('question', $question)
            ->getArrayResult();
    }

    /**
     * @param Question|int $question
     * @return iterable<?string>
     */
    public function getText(Question|int $question): iterable
    {
        $dql = 'select a.freeText from App\Entity\Answer a where a.question = :question';

        /** @var iterable<array<string, ?string>> $chunks */
        $chunks = $this->getEntityManager()->createQuery($dql)
            ->setParameter('question', $question)
            ->toIterable();

        foreach ($chunks as $chunk) { // unwrap results
            yield $chunk['freeText'];
        }
    }
}
