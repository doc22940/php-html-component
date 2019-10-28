<?php

namespace Eightfold\HtmlComponent\Tests;

use Eightfold\HtmlComponent\Tests\BaseTest;

use Eightfold\HtmlComponent\Component;

class ComponentTest extends BaseTest
{

    public function testHtmlComponent()
    {
        $expected = '<html id="my-component"></html>';
        $result = Component::html()->compile('id my-component');
        $this->assertEquality($expected, $result);
    }

    public function testParagraphSpanComponent()
    {
        $expected = '<p><span>Hello, World!</span></p>';
        $result = Component::p(
            Component::span(
                Component::text('Hello, World!')
            )
        )->compile();
        $this->assertEquality($expected, $result);
    }

    public function testButtonWebComponentExtension()
    {
        $expected = '<button is="my-button">Save</button>';
        $result = Component::my_button(
                Component::text('Save')
            )->extends('button')->compile();
        $this->assertEquality($expected, $result);
    }

    public function testPage()
    {
        $expected = '<html><head><title>Hello, World!</title><style></style></head><body><img src="http://example.com" alt="A picture of the world"><p is="my-component">Hello, World!</p><my-link href="http://example.com/domination">World Domination</my-link><p>Done!</p></body></html>';
        $result = Component::html(
                  Component::head(
                      Component::title(Component::text('Hello, World!'))
                    , Component::style()
                  )
                , Component::body(
                      Component::img()
                        ->omitEndTag()
                        ->attr('src http://example.com', 'alt A picture of the world')
                    , Component::my_component(Component::text('Hello, World!'))
                        ->extends('p')
                    , Component::my_link(Component::text('World Domination'))
                        ->attr('href http://example.com/domination')
                    , Component::text('<p>Done!</p>')
                  )
            )->compile();
        $this->assertEquality($expected, $result);
    }
}
