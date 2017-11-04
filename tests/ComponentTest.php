<?php

namespace Eightfold\HtmlComponent\Tests;

use Eightfold\HtmlComponent\Tests\BaseTest;

use Eightfold\HtmlComponent\Component;

class ComponentTest extends BaseTest
{
    public function testHtmlComponent()
    {
        $expected = '<html id="my-component"></html>';
        $result = Component::html(true)->compile('id my-component');
        $this->assertEquality($expected, $result);
    }

    public function testParagraphSpanComponent()
    {
        $expected = '<p><span>Hello, World!</span></p>';
        $result = Component::p(
            Component::span('Hello, World!')
        )->compile();
        $this->assertEquality($expected, $result);
    }

    public function testButtonWebComponentExtension()
    {
        $expected = '<button is="my-button">Save</button>';
        $result = Component::my_button('Save', 'button')->compile();
        $this->assertEquality($expected, $result);
    }

    public function testPage()
    {
        $expected = '<html><head><title>Hello, World!</title><style></style></head><body><img src="http://example.com" alt="A picture of the world"><p is="my-component">Hello, World!</p><my-link href="http://example.com/domination">World Domination</my-link><p>Done!</p></body></html>';
        $result = 
            Component::html([
                Component::head([
                    Component::title('Hello, World!'),
                    Component::style(true)
                ]),
                Component::body([
                    Component::img(false)
                        ->attr('src http://example.com', 'alt A picture of the world'),
                    Component::my_component('Hello, World!', 'p'),
                    Component::my_link('World Domination')
                        ->attr('href http://example.com/domination'),
                    '<p>Done!</p>'
                ])
            ])->compile();
        $this->assertEquality($expected, $result);
    } 
}
