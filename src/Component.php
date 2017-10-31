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
    public static function make($content = true, array $attributes = [], string $component = '', string $extends = ''): string
    {
        $config = [];
        if (is_bool($content) && ! $content) {
            $config['omit-end-tag'] = true;

        } elseif (is_string($content)) {
            $config['content'] = $content;

        } elseif (is_array($content)) {
            $config['content'] = '';
            foreach ($content as $maker) {
                $config['content'] .= $maker;

            }
        }

        if (strlen($component) == 0) {
            return '';
        }

        $config['attributes'] = $attributes;
        $config['element'] = $component;
        $config['extends'] = $extends;

        return self::build($config);
    }

    /**
     * Compiles HTML (XML) string based on configuration input.
     *
     * @param  array  $config [description]
     * @return [type]         [description]
     */
    public static function build(array $config = []): string
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
        if (isset($config['extends']) && strlen($config['extends']) > 0) {
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
            $pairs = implode(' ', array_map(function($key, $value) {
                // Booleans can be `key` or `key="key"`
                // HTML5 prefers just `key`.
                if ($key == $value) {
                    return $key;
                }

                if (strlen($value) > 0) {
                    return $key .'="'. $value .'"';    
                }
            }, array_keys($attributes), array_values($attributes)));
            if (strlen($pairs) > 1) {
                $html .= ' '. $pairs;

            }
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

    /** 2.0 */
    /**
     * One of the things I like about 8fold UI Kit is the call flow:
     *
     * UIKit::p('content', ['attributes'], 'component');
     *
     * We know who we're calling. We know what element should be returned. We have the
     * the attributes, which are optional. We have the ability to use a web component.
     * What I don't like is actually something I read about a lot of HTML generators, 
     * which is that optional attributes part. It's not a bad design decision and it 
     * still allows for the need to do something like this:
     *
     * UIKit::p('content', [], 'my-paragraph');
     *
     * This is one of the reasons we moved to an array setup in the first place. Call
     * signature that, arguably do too much, fall victim to this quite often. You end
     * up using the default value because you don't need it, but you do need the next
     * one.
     *
     * The drawback on the 8fold Component is similar:
     *
     * Component::make('content', [], 'p', 'my-paragraph');
     *
     * Got me thinking, what if we could incorporate the dynamic nature of the kit?
     *
     * The purpose of 8fold Component is to generate web components. Web components 
     * need to be hyphenated to allow a namespace. Having said that, function names
     * cannot be hyphenated; however, they can include underscores.
     *
     * Component::my_paragraph('content', [], 'p');
     *
     * That gets rid of one argument but we still have the attributes problem. This is
     * problem that can easily be solved by using instantiation and method chaining:
     *
     * Component::my_paragraph('content', 'p')->attributes([]);
     *
     * Having said that, we lost the automatic compilation required by using static
     * methods, because we are returning the instance and not the string.
     *
     * Component::my_paragraph('content', 'p')->attributes([])->compile();
     *
     * This is pretty readable. I need `my-paragraph` with some content, it extends the
     * normal HTML `p` element, it should have the following attributes. Go ahead and
     * compile it because I'm done defining it.
     * 
     * This is pretty normal for HTML generators using PHP. Having said that, something
     * also pretty normal is that the `compile` or `render` or `squirrel` method that
     * actually compiles the string, never takes an argument. Why not put the 
     * attributes there?
     *
     * Component::my_paragraph('content', 'p')->compile([]);
     *
     * We could make all the attributes method calls as well, but that doesn't really
     * save us anything (and comes with its own set of problems):
     *
     * Component::my_paragraph('content', 'p')->class('my-pararaph')->compile();
     *
     * Compare that to using an array:
     *
     * Component::my_paragraph('content', 'p')
     *   ->attributes(['class' => 'my-paragraph'])
     *   ->compile();
     *
     * We get rid of the square brackets. Aesthetically the non-array one is easier on 
     * the eyes (the array is one of the least aesthetically considered piece of PHP).
     * You are still typing out the attribute name though and, to be fair, you can't
     * get away from that really. But, you can still modify the array:
     *
     * Component::my_paragraph('content', 'p')
     *   ->attributes('class.my-paragraph', 'id.my-unique-id')
     *
     * So, instead of typeing `=>` or '->', which are effevtivly the same thing, you 
     * type `.`. The square brackets are still deleted. And, unlike with method 
     * chaining, the attributes are all contained in one place.
     *
     * 
     *
     */
    private $_element = '';
    private $_extends = '';
    private $_content = true;
    private $_attributes = [];

    static public function __callStatic(string $element, array $args)
    {
        if ($element == 'make') {
            die('make call');

        } elseif ($element == 'build') {
            die('build call');

        }

        $extends = (isset($args[1]))
            ? $args[1]
            : '';
        return self::createInstance($args[0], $element, $extends);

        die($element);
        $class = static::classForElement($element);

        if (self::shouldUseMake($args)) {
            $content = $args[0];
            $attributes = (isset($args[1])) ? $args[1] : [];
            $component = (isset($args[2]))  ? $args[2] : '';
            $extends = (isset($args[3]))    ? $args[3] : '';

            return $class::make($content, $attributes, $component, $extends);
        }
        $config = [];
        if (isset($args[0])) {
            $config = $args[0];
        }
        return $class::build($config);
    }

    private static function createInstance($content, string $extends = ''): Component
    {
        $instance = new Component($content, $extends);
        return $instance;
    }

    private function __construct($content, string $element = '', string $extends = '')
    {
        $this->_element = $element;
        $this->_extends = $extends;
        $this->_content = $content;
    }

    public function compile(string ...$attributes)
    {
        $attributeString = '';
        if (count($attributes) > 0) {
            $atts = [];
            foreach ($attributes as $attribute) {
                $atts[] = str_replace('.', '="', $attribute) .'"';

            }
            $attributeString = ' '. implode(' ', $atts);
        }

        return '<'. $this->_element . $attributeString .'>'. 
            $this->_content .
            '</'. $this->_element .'>';
    }
}