<?php declare(strict_types=1);

namespace App\Tests\Utility;

use App\Utility\Stats;
use PHPUnit\Framework\TestCase;

class StatsTest extends TestCase
{
    /**
     * @return mixed[]
     */
    public function provideDistributions(): array
    {
        $polar = [['option' => 0, 'cnt' => 10], ['option' => 10, 'cnt' => 10]];
        $notAnswered = [['option' => null, 'cnt' => 10]];
        $empty = [];

        return [
            [$polar, 5.0],
            [$notAnswered, 0.0],
            [$empty, 0.0],
        ];
    }

    /**
     * @dataProvider provideDistributions
     * @param mixed[] $distribution
     * @param float $expected
     * @return void
     */
    public function testAverage(array $distribution, float $expected): void
    {
        $actual = Stats::average($distribution);
        self::assertEqualsWithDelta($expected, $actual, 0.01);
    }

    public function testUpdateWordFrequency(): void
    {
        $text = "/.twice\nonce   x; twice";
        $dictionary = [];

        Stats::updateWordFrequency($dictionary, $text);

        self::assertArrayHasKey('twice', $dictionary);
        self::assertArrayHasKey('once', $dictionary);
        self::assertArrayNotHasKey('x', $dictionary);

        self::assertEquals(2, $dictionary['twice']);
        self::assertEquals(1, $dictionary['once']);

        $text = "another answer same dictionary";

        // now with non-empty dictionary
        Stats::updateWordFrequency($dictionary, $text);

        // new words appended
        self::assertArrayHasKey('another', $dictionary);

        // old words still there
        self::assertEquals(2, $dictionary['twice']);
    }
}
