<?php

namespace Eightfold\HtmlComponent\Instance;

use Eightfold\HtmlComponent\Interfaces\Compile;

use Eightfold\HtmlComponent\Traits\HasParent;

class Text implements Compile
{
    use HasParent;

    private $content = '';

    static public function make(string $content)
    {
        return new Text($content);
    }

    private function __construct(string $content = '')
    {
        $this->content = $content;
    }

    public function compile(string ...$attributes): string
    {
        return $this->content;
    }
}