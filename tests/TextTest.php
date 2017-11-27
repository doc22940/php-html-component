<?php

namespace Eightfold\HtmlComponent\Tests;

use Eightfold\HtmlComponent\Tests\BaseTest;

use Eightfold\HtmlComponent\Instance\Text;

class TextTest extends BaseTest
{
    public function testTextReturnsExpectedString()
    {
        $expected = 'Hello';
        $result = Text::make('Hello')->compile();
        $this->assertEquality($expected, $result);
    }   
}
