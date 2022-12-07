<?php

namespace App\DataFixtures;

use App\Contract\QuestionType;
use App\Entity\Answer;
use App\Entity\Question;
use App\Entity\Survey;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    // need it for raw connection access
    public function __construct(private EntityManagerInterface $em) { }

    /**
     * generate a survey with 10 questions (9 range + 1 free text) and 100.000 random answers for each of them
     *
     * @param ObjectManager $_ unused though
     * @return void
     * @throws Exception
     */
    public function load(ObjectManager $_): void
    {
        $questions = [];
        $n = 1;
        while ($n < 10) {
            $question = new Question("Rate PHP $n:", QuestionType::SINGLE_05_RANGE);
            $questions[] = $question;
            $this->em->persist($question);
            $n++;
        }

        $question = new Question("Comment:", QuestionType::FREE_TEXT);
        $questions[] = $question;
        $this->em->persist($question);

        $this->em->flush();

        $survey = new Survey("About PHP", $questions);
        $this->em->persist($survey);
        $this->em->flush();

        // just to have it typechecked. so Answer structure changes less likely go unnoticed
        new Answer(new Question('dummy', QuestionType::FREE_TEXT), null, null);

        // source for free text question
        $randomText = file_get_contents(__FILE__);
//todo: make sure asserts enabled in php.ini
        assert($randomText !== false);

        $randomText = str_replace(',', ' ', $randomText); // lame way to reserve , for separator
        $randomText = explode(PHP_EOL, $randomText);
        $nLines = count($randomText) - 1;

        // prepare answers as temporary csv file to load into db at once. seems more fun than constructing bulk inserts
        $path = tempnam('/tmp', '');
        assert($path !== false);

        $handle = fopen($path,'w');
        assert($handle !== false);

        foreach ($questions as $q) {
            $id = $q->getId();
            $i = 100_000;
            while ($i > 0) {
                $i--;
                if ($q->getType() === QuestionType::SINGLE_05_RANGE) {
                    $fixed = random_int(0, 5);
                    $text = '\N'; // \N — mysql NULL
                } else {
                    $fixed = '\N';
                    $text = $randomText[random_int(0, $nLines)];
                }

                fwrite($handle, "$id,$fixed,$text\n"); // that's why , is reserved
            };

        }
        fclose($handle);

        // not very safe (trust free text source)
        $conn = $this->em->getConnection();
        $conn->executeStatement("
            SET GLOBAL local_infile=1; 
            LOAD DATA LOCAL INFILE '$path' INTO TABLE answer
            FIELDS TERMINATED BY ','
            (question_id, fixed_numeric, free_text)");
//todo: del temp file
    }
}
