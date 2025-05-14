<?php

namespace App\Http\Common\utils;

class TextFormatter
{
    private string $text;

    public function __construct(string $text)
    {
        $this->text = $text;
    }

    public function properCase(): string
    {
        $lowerWords = ['dan', 'di', 'ke', 'dari', 'yang', 'atau'];

        $words = explode(' ', strtolower($this->text));

        $result = array_map(function ($word) use ($lowerWords) {
            return in_array($word, $lowerWords) ? $word : ucfirst($word);
        }, $words);

        return implode(' ', $result);
    }
}
