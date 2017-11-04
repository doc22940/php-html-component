<?php

namespace Eightfold\HtmlComponent\Instance;

/**
 * Component
 *
 * This is the string compiler. Because Component needs to allow for all sorts of
 * posibilities, it doesn't really assert any opinions how things *should* be.
 *
 */
class Component
{
    protected $_element = '';
    protected $_extends = '';
    protected $_role = '';
    protected $_content;
    protected $_attributes = [];

    /**
     * @todo During the compile we whould be able to set a parameter on any Components
     *       being processed. Namely, the component that is about to make it a direct
     *       descendent. This would allow extensions, like 8fold Elements verify that
     *       the descendent is valid prior to compilation and then do with that info.
     *       what they will.
     * @var null
     */
    protected $_parent = null;
    
    /**
     * Instantiates Component with the bare bones definition required.
     *
     * @param  bool|array|string $content (Default is true) True means the component
     *                                    accepts content and will have a closing tag.
     *                                    False means the component is self-closing and
     *                                    will not have a closing tag. An array means
     *                                    the component accepts content; the array may
     *                                    contain strings, Component instances, or a 
     *                                    combination of the two. A string means the
     *                                    component accepts content, and you want that
     *                                    string to *be* the content.
     * @param  string            $element The text that will most likely be used in the
     *                                    opening and closing tags. Ex. `html` becomes
     *                                    `<html></html>`. If you use the main 
     *                                    Component factory entry, it will be the 
     *                                    method name.
     * @param  string            $extends If set, will be used in the opening and
     *                                    closing tags, which will cause `element` to
     *                                    placed in the `is` attribute of the 
     *                                    component. Ex. `my-html` `html` becomes
     *                                    `<html is="my-html"></html>`.
     * 
     * @return Component         [description]
     */
    final public static function createInstance($content = true, string $element, string $extends =''): Component
    {
        $instance = new static($content, $element, $extends);
        return $instance;
    }

    /**
     * @see createInstance()
     */
    private function __construct($content, string $element, string $extends = '')
    {
        $this->_element = $element;
        $this->_extends = $extends;
        $this->_content = $content;
    }

    /**
     * Assign the parent to the child.
     * 
     * @param  Component $component [description]
     * @return [type]               [description]
     */
    private function parent(Component $component)
    {
        $this->_parent = $component;
        return $this;
    }

    /**
     * Get the parent for the child.
     * 
     * @return [type] [description]
     */
    protected function getParent(): ?Component
    {
        return $this->_parent;
    }

    /**
     * Get the element name of the component.
     * 
     * @return [type] [description]
     */
    public function getElement(): string
    {
        return $this->_element;
    }    

    /**
     * Terminating method that builds the component string and returns it.
     * 
     * @param  strings $attributes See attr()
     * 
     * @return string              The compiled web component string.
     */
    public function compile(string ...$attributes): string
    {
        if (count($attributes) > 0) {
            $this->attr(...$attributes);
        }

        // opening:
        // <element/extends attributes> content </element/extends>
        $elementName = ($this->isWebComponent())
            ? $this->_extends
            : $this->getElementName();

        $attributes = $this->compileAttributes();

        $opening = '<'. $elementName;
        if (strlen($attributes) > 0) {
            $opening .= ' '. $attributes;
        }
        $opening .= '>';

        $content = $this->compileContent($this->_content);
        
        $closing = ($this->hasEndTag())
            ? '</'. $elementName .'>'
            : '';

        return $opening . $content . $closing;
    }

    /**
     * Print the compiled string for the component.
     * 
     * @param  string $attributes See compile()
     */
    public function print(string ...$attributes)
    {
        print $this->compile(...$attributes);
    }

    /**
     * Print the compiled string for the component.
     * 
     * @param  string $attributes See compile()
     */
    public function echo(string ...$attributes)
    {
        $this->print(...$attributes);
    }

    /**
     * Adds a role attribute as the first or second attribute depending on if the 
     * component is an extension of something else.
     * 
     * @param  string    $role The value for the attribute.
     * @return Component
     */
    public function role(string $role): Component
    { 
        $this->_role = $role;
        return $this;
    }    

    /**
     * Overwrite the content of the component.
     * 
     * @param  [type] $content [description]
     * @return [type]          [description]
     *
     * @todo Discuss further
     */
    private function content($content): Component
    {
        $this->_content = $content;
        return $this;
    }

    /**
     * Set any number of attributes to the component.
     *
     * Duplications will be overwritten.
     * 
     * @param  string $attributes The name of the attribute `id` followed by a single 
     *                            space, followed by the value for the attribute. Ex.
     *                            `id something` becomes `id="something"`.
     * @return Component
     */
    public function attr(string ...$attributes): Component
    {
        foreach ($attributes as $attribute) {
            $this->addAttribute($attribute);
        }
        return $this;
    }

    /**
     * Set a single attribute value for the component.
     *
     * Duplications will be overwritten.
     * 
     * @param string $attribute See attr()
     */
    private function addAttribute(string $attribute)
    {
        list($key, $value) = explode(' ', $attribute, 2);
        $this->_attributes[$key] = $value;
    }

    /**
     * Kebab-case the element passed to the Component.
     *
     * Because we typically receive the element name from a function name, it will
     * most likely be underscored not hyphenated.
     * 
     * @return string The kebab-cased element name.
     */
    private function getElementName(): string
    {
        return str_replace('_', '-', $this->_element);
    }

    /**
     * Recursively build the component content string from the inside out.
     *
     * @param  any    $contentToCompile See createInstance() content.
     * @return string                   The compiled content string.
     */
    private function compileContent($contentToCompile): string
    {
        $content = '';
        if ($contentToCompile instanceof Component) {
            $content = $contentToCompile->parent($this)->compile();

        } elseif (is_string($contentToCompile)) {
            $content = $contentToCompile;

        } elseif (is_array($contentToCompile)) {
            foreach ($contentToCompile as $maker) {
                $content .= $this->compileContent($maker);

            }
        }  
        return $content;
    }

    /**
     * Build the component's list of attributes.
     * 
     * @return string The compiled string of attributes of the form id="something".
     */
    private function compileAttributes(): string
    {
        $attributes = '';

        $prefixed = [];
        if ($this->isWebComponent()) {
            $prefixed['is'] = $this->getElementName();
        }

        if (strlen($this->_role) > 0) {
            $prefixed['role'] = $this->_role;
        }

        $mergedAttributes = $this->_attributes;
        if (count($prefixed) > 0) {
            $mergedAttributes = array_merge($prefixed, $mergedAttributes);
        }
        
        if ($mergedAttributes > 0) {
            $preparedAttributes = [];
            foreach ($mergedAttributes as $key => $value) {
                var_dump($value);
                if (strlen($value) > 0) {
                    $preparedAttributes[] = $key .'="'. $value .'"';    
                }
            }
            $attributes = implode(' ', $preparedAttributes);
        }
        return $attributes;
    }

    /**
     * Whether this component is an extension of some other component.
     *
     * If this component is the extension of something else, the extension is used as
     * the element name. Ex. $element = 'my-component', $extends = 'p' becomes
     * <p is="my-component"></p> not <my-component is="p"></my-component>
     * 
     * @return boolean Whether both `element` and `extends` have been set on the 
     *                 component.
     */
    private function isWebComponent(): bool
    {
        return (strlen($this->_element) > 0 && strlen($this->_extends) > 0);
    }

    /**
     * Whether this component should generate an end tag.
     * 
     * @return boolean Whether this component should generate an end tag.
     */
    private function hasEndTag(): bool
    {
        return ( ! is_bool($this->_content) || $this->_content);
    }
}