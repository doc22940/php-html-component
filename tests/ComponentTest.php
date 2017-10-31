<?php

namespace Eightfold\HtmlComponent\Tests;

use Eightfold\HtmlComponent\Tests\BaseTest;

use Eightfold\HtmlComponent\Component;

class ComponentTest extends BaseTest
{
    public function testMakeEmpty()
    {
        $expected = '';
        $result = Component::make();
        $this->assertEquality($expected, $result);
    }

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

    public function testMakeHtmlComponent()
    {
        $expected = '<html id="my-component"></html>';
        $result = Component::make(true, ['id' => 'my-component'], 'html');
        $this->assertEquality($expected, $result);
    }

    public function testHtmlComponent2()
    {
        $expected = '<html id="my-component"></html>';
        $result = Component::html(false)->compile('id.my-component');
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

    public function testMakeParagraphSpanComponent()
    {
        $expected = '<p><span>Hello, World!</span></p>';
        $result = Component::make(
            Component::make('Hello, World!', [], 'span'),
            [],
            'p'
        );
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

    public function testMakeButtonWebComponentExtension()
    {
        $expected = '<button is="my-button">Save</button>';
        $result = Component::make('Save', [], 'my-button', 'button');
        $this->assertEquality($expected, $result);
    }

    public function testMakePage()
    {
        $expected = '<html><head><title>Hello, World!</title></head><body><p is="my-component">Hello, World!</p></body></html>';
        $result = Component::make([
            Component::make(
                Component::make('Hello, World!', [], 'title')
            , [], 'head'),

            Component::make(
                Component::make('Hello, World!', [], 'my-component', 'p')
            , [], 'body')

        ], [], 'html');
        $this->assertEquality($expected, $result);
    }  
}
