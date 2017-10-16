<?php

namespace Eightfold\HtmlComponent\Tests;

use Eightfold\HtmlComponent\Tests\BaseTest;

use Eightfold\HtmlComponent\Component;

class ComponentTest extends BaseTest
{
    public function testHtmlComponent()
    {
        $expected = '<html id="my-component"></html>';
        $result = Component::build([
            'element' => 'html',
            'self-closing' => false,
            'attributes' => [
                'id' => 'my-component'
            ]
        ]);
        $this->assertEquality($expected, $result);
    }

    public function testParagraphSpanComponent()
    {
        $expected = '<p><span>Hello, World!</span></p>';
        $result = Component::build([
            'element' => 'p',
            'omit-closing-tag' => false,
            'content' => [
                [
                    'element' => 'span',
                    'omit-closing-tag' => false,
                    'content' => 'Hello, World!'
                ]
            ]
        ]);
        $this->assertEquality($expected, $result);
    }

    public function testButtonWebComponentExtension()
    {
        $expected = '<button is="my-button">Save</button>';
        $result = Component::build([
            'element' => 'my-button',
            'extends' => 'button',
            'content' => 'Save'
        ]);
        $this->assertEquality($expected, $result);
    }
}
