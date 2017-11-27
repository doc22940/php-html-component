<?php

namespace Eightfold\HtmlComponent\Tests;

use Eightfold\HtmlComponent\Tests\BaseTest;

use Eightfold\HtmlComponent\Instance\Component;
use Eightfold\HtmlComponent\Instance\Text;

class ComponentInstanceTest extends BaseTest
{
    public function testHtmlComponentMake()
    {
        $expected = '<html id="my-component"></html>';
        $result = Component::make('html', ['id my-component'])->compile();
        $this->assertEquality($expected, $result);
    }

    public function testParagraphSpanComponentMake()
    {
        $expected = '<p><span>Hello, World!</span></p>';
        $result = Component::make(
              'p'
            , []
            , Component::make(
                  'span'
                , []
                , Text::make('Hello, World!')
            )
        )->compile();
        $this->assertEquality($expected, $result);
    }
}