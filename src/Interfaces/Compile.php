<?php

namespace Eightfold\HtmlComponent\Interfaces;

interface Compile
{
    public function compile(string ...$attributes): string;
}