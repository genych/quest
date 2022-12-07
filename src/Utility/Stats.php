<?php declare(strict_types=1);

namespace App\Utility;

class Stats
{
    public static function updateWordFrequency(array &$dictionary, string $text): void
    {
        // just English letters, spaces and dashes. barbaric!
        $text = preg_replace('/[^a-zA-Z -]+/', ' ', $text);

        $words = explode(' ', $text);
        foreach ($words as $word) {
            $word = trim($word);
            if (mb_strlen($word) < 2) { // and only 2+ lettered words!
                continue;
            }
            if (array_key_exists($word, $dictionary)) {
                $dictionary[$word]++;
            } else {
                $dictionary[$word] = 1;
            }
        }
    }

    /**
     * @param array<int, array<string, ?int>> $distribution
     * @return float
     */
    public static function average(array $distribution): float
    {
        $n = 0;
        $acc = 0;
        foreach ($distribution as $_ => ['option' => $value, 'cnt' => $count]) {
            if ($value === null) { // skip unanswered
                continue;
            }
            $n += $count;
            $acc += $value * $count;
        }

        if ($n === 0) {
            return 0.0;
        }
        return $acc / $n;
    }
}
