<?php

namespace Eightfold\HtmlComponent;

use Eightfold\HtmlComponent\Instance\Component as InstanceComponent;
use Eightfold\HtmlComponent\Instance\Text;

/** 
 * Component
 *
 * A featherweight library for generating strings for HTML, web components, and (most
 * likely) XML.
 *
 * Component::element('Hello, World!', 'p')->attr('attr-name value')->compile()
 * 
 * Component::element('Hello, World!', 'p')->attr('attr-name value')->print()
 *
 * Component::element('Hello, World!', 'p')->attr('attr-name value')->echo()
 *
 * Component::element('Hello, World!', 'p')->echo('attr-name value')
 *
 * Output:
 *
 * <p is="element" attr-name="value">Hello, World!</p>
 *
 *
 * Note: compile() will return the string, without printing.
 *
 * @see Eightfold\HtmlComponent\Instance\Component
 * 
 */
abstract class Component
{
    /**
     * Intercept all static calls.
     *
     * Used to convert the method name to the element name. The first argument becomes
     * the content, the second argument becomes the element being extended.
     *
     * @see Eightfold\HtmlComponent\Instance\Component
     * 
     * @param  string $element [description]
     * @param  array  $args    [description]
     * @return [type]          [description]
     */
    public static function __callStatic(string $element, array $args)
    {
        if (isset($args[0]) && is_bool($args[0])) {
            $content = $args[0];
        if ($element == 'text') {
            if (isset($args[0])) {
                return Text::make($args[0]);    
            }
        }
        return InstanceComponent::make($element, [], ...$args);
        // die(var_dump($args));
        // $content = true;
        // if (isset($args[0]) && is_bool($args[0])) {
        //     $content = $args[0];

        // } elseif (isset($args[0])) {
        //     $content = $args[0];

        // }

        // $extends = (isset($args[1]))
        //     ? $args[1]
        //     : '';

        // return InstanceComponent::createInstance($content, $element, $extends);
    }
}