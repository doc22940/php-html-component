<?php

namespace Eightfold\HtmlComponent;

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
     * Experimental:
     * 
     * After a while of using 8fold HTML in conjunction with 8fold Component, it seemed
     * an alternative would be necessary. The avoidance of future annoyance by using an
     * associative array instead of a fixed call signature did not outweigh the pain
     * of typing the keys:
     *
     * ```
     * Component::build([
     *   'omit-end-tag' => false,
     *   'element'      => 'my-button',
     *   'extends'      => 'button',
     *   'attributes'   => [
     *     'class' => 'all-this-for-that'
     *   ] 
     * ]);
     *
     * <button is="my-button"></button>
     * ```
     * 
     * Why have the above, when you can just do this?
     *
     * ```
     * Component::make(true, ['class' => 'all-this-for-that'], 'my-button', 'button');
     * ```
     *
     * You might be asking why have the component be after the content and attributes.
     * It's because the habit we want to establish in the subsequent extensions is that
     * content is first and attributes second, because the extensions handle the rest.
     *
     * ```
     * Extensions::p('Hello, World!', ['id' => 'main-paragraph']);
     *
     * <p id="main-paragraph">Hello, World!</p>
     * ```
     * 
     * @param  bool|array|string $content    If `false`, will omit the end tag. If 
     *                                       `true` will use the end tag, but there
     *                                       will not be anything in-between the tags.
     *                                       If anything else (array or string), will 
     *                                       assume component has end tag, and will be
     *                                       used for the content between them.
     * @param  array  $attributes [description]
     * @param  string $component  [description]
     * @param  string $extends    [description]
     * @return [type]             [description]
     */
    // public static function make($content = true, array $attributes = [], string $component = '', string $extends = ''): string
    // {
    //     $config = [];
    //     if (is_bool($content) && ! $content) {
    //         $config['omit-end-tag'] = true;

    //     } elseif (is_string($content)) {
    //         $config['content'] = $content;

    //     } elseif (is_array($content)) {
    //         $config['content'] = '';
    //         foreach ($content as $maker) {
    //             $config['content'] .= $maker;

    //         }
    //     }

    //     if (strlen($component) == 0) {
    //         return '';
    //     }

    //     $config['attributes'] = $attributes;
    //     $config['element'] = $component;
    //     $config['extends'] = $extends;

    //     return self::build($config);
    // }

    /**
     * Compiles HTML (XML) string based on configuration input.
     *
     * @param  array  $config [description]
     * @return [type]         [description]
     */
    // public static function build(array $config = []): string
    // {
    //     $html = self::opening($config);
    //     $html .= static::content($config);
    //     $html .= self::closing($config);
    //     return $html;
    // }

    /**
     * Generates the opening for the element. Ex. <html>.
     * 
     * @param  array  &$config [description]
     * @return [type]          [description]
     */
    // static private function opening(array &$config): string
    // {
    //     if (!isset($config['attributes'])) {
    //         $config['attributes'] = [];

    //     }

    //     $ordered = [];
    //     $html = '<'. $config['element'];
    //     if (isset($config['extends']) && strlen($config['extends']) > 0) {
    //         $html = '<'. $config['extends'];
    //         if (isset($config['attributes']['is'])) {
    //             unset($config['attributes']['is']);
    //         }
    //         $ordered['is'] = $config['element'];
    //         $config['element'] = $config['extends'];
    //     }

    //     if (isset($config['role'])) {
    //         if (isset($config['attributes']['role'])) {
    //             unset($config['attributes']['role']);
    //         }
    //         $ordered['role'] = $config['role'];
    //     }

    //     $config['attributes'] = array_merge($ordered, $config['attributes']);

    //     if (isset($config['attributes'])) {
    //         $html .= self::attributes($config);
    //     }
    //     $html .= '>';
    //     return $html;        
    // }

    /**
     * Concatenates the attributes into a string. Ex. id="foo" class="bar".
     * 
     * @param  array  &$config [description]
     * @return [type]          [description]
     */
    // static private function attributes(array &$config): string
    // {
    //     $html = '';
    //     if (isset($config['attributes']) && $attributes = $config['attributes']) {
    //         $pairs = implode(' ', array_map(function($key, $value) {
    //             // Booleans can be `key` or `key="key"`
    //             // HTML5 prefers just `key`.
    //             if ($key == $value) {
    //                 return $key;
    //             }

    //             if (strlen($value) > 0) {
    //                 return $key .'="'. $value .'"';    
    //             }
    //         }, array_keys($attributes), array_values($attributes)));
    //         if (strlen($pairs) > 1) {
    //             $html .= ' '. $pairs;

    //         }
    //     }
    //     return $html;
    // }

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
    // static protected function content(array $config): string
    // {
    //     $html = '';
    //     if (isset($config['content'])) {
    //         if (is_array($config['content'])) {
    //             foreach ($config['content'] as $contentConfig) {
    //                 $html .= static::build($contentConfig);   

    //             }

    //         } elseif (is_string($config['content'])) {
    //             $html .= $config['content'];

    //         }
    //     }
    //     return $html;
    // }

    /**
     * Generates the closing tag for the element. Ex. </html>
     * 
     * @param  array  &$config [description]
     * @return [type]          [description]
     */
    // static private function closing(array &$config): string
    // {
    //     $html = '';
    //     $requiresEndTag = (isset($config['omit-end-tag']) && !$config['omit-end-tag']);
    //     $omitEndTagNotSet = !isset($config['omit-end-tag']);
    //     if ($requiresEndTag || $omitEndTagNotSet) {
    //         $html .= '</'. $config['element'] .'>';
    //     }
    //     return $html;
    // }

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
    private static function deprecatedBuild(string $element, array $args)
    {
        $config = $args[0];
        $realElement = $config['element'];
        $extends = (isset($config['extends']))
            ? $config['extends']
            : '';
        $content = '';
        if (isset($config['content']) && is_array($config['content'])) {
            $content = self::deprecatedBuild($realElement, $config['content']);

        } elseif (isset($config['content']) && is_string($config['content'])) {
            $content = $config['content'];

        }

        $precompileAttributes = (isset($config['attributes']))
            ? $config['attributes']
            : [];
        return self::make($content, $precompileAttributes, $realElement, $extends);
    }

    /** 2.0 */
    protected $_element = '';
    protected $_extends = '';
    protected $_role = '';
    protected $_content;
    protected $_attributes = [];

    /**
     * Intercept all static calls.
     *
     * 
     * @param  string $element [description]
     * @param  array  $args    [description]
     * @return [type]          [description]
     */
    static public function __callStatic(string $element, array $args)
    {
        if ($element == 'make') {
            return self::deprecatedMake($element, $args);

        } elseif ($element == 'build') {
            return self::deprecatedBuild($element, $args);

        }

        $extends = (isset($args[1]))
            ? $args[1]
            : '';
        return self::createInstance($args[0], $element, $extends);
    }

    private static function createInstance($content, $element, $extends): Component
    {
        $instance = new Component($content, $element, $extends);
        return $instance;
    }

    private function __construct($content, string $element, string $extends = '')
    {
        $this->_element = $element;
        $this->_extends = $extends;
        $this->_content = $content;
    }

    public function attr(string ...$attributes): Component
    {
        $this->_attributes = [];
        foreach ($attributes as $value) {
            list($attr, $text) = explode(' ', $value, 2);
            $this->_attributes[] = $attr .'="'. $text .'"';
        }
        return $this;
    }

    public function compile(string ...$attributes): string
    {        
        $element = str_replace('_', '-', $this->_element);
        $content = $this->compileContent();
        $attributes = $this->compileAttributes();

        if (self::isWebComponent()) {    
            $opening = '<'. $this->_extends .' is="'. $element .'"' . $attributes .'>';
            if ($this->hasEndTag()) {
                return $opening . $content . '</'. $this->_extends .'>';            
            }
            return $opening;
        }

        $opening = '<'. $element . $attributes .'>';
        if ($this->hasEndTag()) {
            return $opening . $content . '</'. $element .'>';            
        }
        return $opening;
    }

    private function compileContent()
    {
        $content = null;
        if ($this->isComponent($this->_content)) {
            $content = $this->_content->compile();

        } elseif (is_bool($this->_content) && ! $this->_content) {
            $content = true;

        } elseif (is_string($this->_content)) {
            $content = $this->_content;

        } elseif (is_array($this->_content)) {
            $content = '';
            foreach ($this->_content as $maker) {
                $content .= (is_string($maker))
                    ? $maker
                    : $maker->compile();

            }
        }  
        return $content;
    }

    private function compileAttributes()
    {
        if (count($attributes) > 0) {
            $this->attr($this->_role, implode(', ', $attributes));
        }

        $attributes = '';

        if (count($this->_attributes) > 0) {
            $attributes = ' '. implode(' ', $this->_attributes);
        }
        return $attributes;
    }

    private function isWebComponent(): bool
    {
        return (strlen($this->_element) > 0 && strlen($this->_extends) > 0);
    }

    private function isComponent($test): bool
    {
        return is_a($test, Component::class);
    }

    private function hasEndTag(): bool
    {
        return ( ! is_bool($this->_content) || $this->_content);
    }
}