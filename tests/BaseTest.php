<?php

namespace Eightfold\HtmlComponent\Tests;

use PHPUnit\Framework\TestCase;

class BaseTest extends TestCase
{
    protected function assertEquality($expected, $result)
    {
       $this->assertTrue($result == $expected, $expected ."\n\n". $result);
    }
}
