<?php

namespace App\Http\Common\Utils;

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

    public function upper(): string
    {
        return strtoupper($this->text);
    }

    public function lower(): string
    {
        return strtolower($this->text);
    }

    public function sentenceCase(): string
    {
        $text = strtolower($this->text);
        return ucfirst($text);
    }

    public function titleCase(): string
    {
        return ucwords(strtolower($this->text));
    }

    public function slugify(): string
    {
        $slug = preg_replace('/[^a-z0-9]+/i', '-', strtolower($this->text));
        return trim($slug, '-');
    }

    public function clean(): string
    {
        return preg_replace('/\s+/', ' ', $this->text);
    }
}
