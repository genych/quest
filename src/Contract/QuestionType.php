<?php

namespace App\Contract;

enum QuestionType: string
{
    case SINGLE_05_RANGE = 'range[0-5]';
    case FREE_TEXT = 'free_text';
}
