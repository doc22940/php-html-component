<?php

namespace Eightfold\HtmlComponent;

use Eightfold\HtmlComponent\Instance\Component as InstanceComponent;

/** 
 * Component
 *
 * A featherweight class for generating strings for HTML, web components, and (most
 * likely) XML.
 *
 * 
 *
 * Required keys
 *
 * - **element:** The string to place in the opening and closing tags. 
 *                Ex. `<html></html>` or `<my-component></my-component>`
 *
 * Optional keys
 *
 * - **extends:**          Whether this element extends another, known element. Ex.
 *                         `<button is="my-component"></button>`
 * - **role:**             The role the component plays in the document or application.
 *                         Ex. `<body role="application">`
 * - **omit-closing-tag:** true|false (Default is false.) Whether the element has a
 *                         closing tag. Ex. `<html></html>` versus `<img>`
 * - **attributes:**       Dictionary of key value pairs to place attributes inside the
 *                         element. Ex. `<html lang="en"></html>`
 * - **content:**          string|array Accepts a single string or an array of 
 *                         component configurations.
 *                         
 * By itself, this will not save a developer time or typing when it comes to building
 * web applications. However, the point of this class is to be extended and abstracted
 * in order to do just that. With your extension you can build in various rules 
 * regarding deprecated attributes, ordering of attributes within elements, and so on.
 * @example
 * 
 * ```
 * [
 *     'element' => 'html',
 *     'attributes' => [
 *         'lang' => 'en'
 *     ]
 *     'omit-closing-tag' => false,
 *     'content' => [
 *         [
 *             'element' => 'head',
 *             'omit-closing-tag' => false,
 *             'content' => [
 *                 [
 *                     'element' => 'title',
 *                     'content' => 'Hello, World!'
 *                 ]
 *             ]
 *         ],
 *         [
 *             'element' => 'body',
 *             'omit-closing-tag' => false,
 *             'content' => [
 *                 [
 *                     'element' => 'my-component',
 *                     'extends' => 'p',
 *                     'content' => 'Hello, World!'
 *                 ]
 *             ]
 *         ]
 *     ]
 * ]
 * ```
 *
 * Outputs:
 *
 * ```
 * <html>
 *     <head>
 *         <title>Hello, World!</title>
 *     </head>
 *     <body>
 *         <p is="my-component">Hello, World!</p>
 *     </body>
 * </html>
 * ```
 *
 * Using make():
 *
 * Component::make([
 * 
 *     Component::make(
 *         Component::make('Hello, World!', [], 'title')
 *     , [], 'head'),
 *     
 *     Component::make(
 *         Component::make('Hello, World!', [], 'my-component', 'p')
 *     , [], 'body')
 *     
 * ], [], 'html');
 */
class Component
{
    /**
     * @deprecated 2.0 The `make` method is deprecated and will be removed when 2.0 is 
     *                 released.
     *                 
     * make($content, array $attributes, string $component, string $extends): string
     *
     */
    private static function deprecatedMake(string $element, array $args)
    {
        if (count($args) == 0) {
            return '';
        }
        
        $realElement = $args[2];
        $extends = '';
        if (isset($args[3])) {
            $realElement = $args[2];
            $extends = $args[3];
        }
        $content = $args[0];

        $attributes = [];
        $precompileAttributes = $args[1];
        foreach ($precompileAttributes as $key => $value) {
            $attributes[] = $key .' '. $value;
        }

        $return = '';
        if (count($attributes) > 0) {
            $attributes = implode(', ', $attributes);
            $return = self::$realElement($content, $extends)
                ->attr($attributes);

        } else {
            $return = self::$realElement($content, $extends);

        }
        return $return->compile();
    }

    /**
     * @deprecated 2.0 The `build` method is deprecated and will be removed when 2.0 
     *                 is released.
     *                 
     * build(array $config): string
     *
     */
    protected static function deprecatedBuild(string $element, array $args)
    {     
        $config = (isset($args[0]))
            ? $args[0]
            : $args;
        if (is_string($config)) {
            return $config;
        }
        // var_dump($config);
        $realElement = (isset($config['element']))
            ? $config['element']
            : $element;
        $extends = (isset($config['extends']))
            ? $config['extends']
            : '';
        $content = '';
        if (isset($config['content']) && is_array($config['content'])) {
            $content = static::deprecatedBuild($realElement, $config['content']);

        } elseif (isset($config['content']) && is_string($config['content'])) {
            $content = $config['content'];

        }

        $precompileAttributes = (isset($config['attributes']))
            ? $config['attributes']
            : [];
           
        return static::make($content, $precompileAttributes, $realElement, $extends);
    }

    /** 2.0 */

    /**
     * Intercept all static calls.
     *
     * 
     * @param  string $element [description]
     * @param  array  $args    [description]
     * @return [type]          [description]
     */
    public static function __callStatic(string $element, array $args)
    {
        if ($element == 'make') {
            return self::deprecatedMake($element, $args);

        } elseif ($element == 'build') {
            return self::deprecatedBuild($element, $args);

        }

        $extends = (isset($args[1]))
            ? $args[1]
            : '';

        $content = '';
        if (count($args) > 0) {
            if (is_bool($args[0])) {
                $content = $args[0];

            } else {
                $content = $args[0];
            }
        }
        return InstanceComponent::createInstance($content, $element, $extends);
    }
}