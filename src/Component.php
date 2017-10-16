<?php

namespace Eightfold\HtmlComponent;

/**
 * @version 1.0.0
 * 
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
 *                Ex. <html></html> or <my-component></my-component>
 *
 * Optional keys
 *
 * - **extends:**          Whether this element extends another, known element. Ex.
 *                         <button is="my-component"></button>
 * - **role:**             The role the component plays in the document or application.
 *                         Ex. <body role="application">
 * - **omit-closing-tag:** true|false (Default is false.) Whether the element has a
 *                         closing tag. Ex. <html></html> versus <img>
 * - **attributes:**       Dictionary of key value pairs to place attributes inside the
 *                         element. Ex. <html lang="en"></html>
 * - **content:**          string|array Accepts a single string or an array of 
 *                         component configurations.
 *
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
 * By itself, this will not save a developer time or typing when it comes to building
 * web applications. However, the point of this class is to be extended and abstracted
 * in order to do just that. With your extension you can build in various rules 
 * regarding deprecated attributes, ordering of attributes within elements, and so on.
 * 
 */
abstract class Component
{
    /**
     * Compiles HTML (XML) string based on configuration input.
     *
     * @param  array  $config [description]
     * @return [type]         [description]
     */
    static public function build(array $config = []): string
    {
        $html = self::opening($config);
        $html .= static::content($config);
        $html .= self::closing($config);
        return $html;
    }

    /**
     * Generates the opening for the element. Ex. <html>.
     * 
     * @param  array  &$config [description]
     * @return [type]          [description]
     */
    static private function opening(array &$config): string
    {
        if (!isset($config['attributes'])) {
            $config['attributes'] = [];

        }

        $ordered = [];
        $html = '<'. $config['element'];
        if (isset($config['extends'])) {
            $html = '<'. $config['extends'];
            if (isset($config['attributes']['is'])) {
                unset($config['attributes']['is']);
            }
            $ordered['is'] = $config['element'];
            $config['element'] = $config['extends'];
        }

        if (isset($config['role'])) {
            if (isset($config['attributes']['role'])) {
                unset($config['attributes']['role']);
            }
            $ordered['role'] = $config['role'];
        }

        $config['attributes'] = array_merge($ordered, $config['attributes']);

        if (isset($config['attributes'])) {
            $html .= self::attributes($config);
        }
        $html .= '>';
        return $html;        
    }

    /**
     * Concatenates the attributes into a string. Ex. id="foo" class="bar".
     * 
     * @param  array  &$config [description]
     * @return [type]          [description]
     */
    static private function attributes(array &$config): string
    {
        $html = '';
        if (isset($config['attributes']) && $attributes = $config['attributes']) {
            $html .= ' '. implode(' ', array_map(function($key, $value) {
                // Booleans can be `key` or `key="key"`
                // HTML5 prefers just `key`.
                if ($key == $value) {
                    return $key;
                }
                return $key .'="'. $value .'"';
            }, array_keys($attributes), array_values($attributes)));
        }
        return $html;
    }

    /**
     * Processes the content of the element under build.
     *
     * If the content value is a string, it will be appended to the compiled string. If
     * the content value is an array, it is assumed each array value is a fully defined
     * configuration for an element or a single string. Therefore, if you are 
     * extending this class, it is recommended that you override this method to apply 
     * any pre-processing you like to the sub-configurations.
     * 
     * @param  array  &$config [description]
     * @return [type]          [description]
     */
    static protected function content(array $config): string
    {
        $html = '';
        if (isset($config['content'])) {
            if (is_array($config['content'])) {
                foreach ($config['content'] as $contentConfig) {
                    $html .= static::build($contentConfig);   

                }

            } elseif (is_string($config['content'])) {
                $html .= $config['content'];

            }
        }
        return $html;
    }

    /**
     * Generates the closing tag for the element. Ex. </html>
     * 
     * @param  array  &$config [description]
     * @return [type]          [description]
     */
    static private function closing(array &$config): string
    {
        $html = '';
        $requiresEndTag = (isset($config['omit-end-tag']) && !$config['omit-end-tag']);
        $omitEndTagNotSet = !isset($config['omit-end-tag']);
        if ($requiresEndTag || $omitEndTagNotSet) {
            $html .= '</'. $config['element'] .'>';
        }
        return $html;
    }
}