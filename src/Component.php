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
        if ($element == 'text') {
            if (isset($args[0])) {
                return Text::make($args[0]);    
            }
        }
        return InstanceComponent::make($element, [], ...$args);
    }
}