<?php

namespace Eightfold\HtmlComponent\Instance;

class Component
{
    protected $_element = '';
    protected $_extends = '';
    protected $_role = '';
    protected $_content;
    protected $_attributes = [];
    
    public static function createInstance($content, string $element, string $extends =''): Component
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

    public function content($content): Component
    {
        $this->_content = $content;
        return $this;
    }

    public function attr(string ...$attributes): Component
    {
        if (count($this->_attributes) > 0) {
            array_push($this->_attributes, $attributes);

        } else {
            $this->_attributes = $attributes;

        }
        return $this;
    }

    public function role(string $role): Component
    { 
        $this->_role = $role;
        return $this;
    }    

    public function compile(string ...$attributes): string
    {
        if (count($attributes) > 0) {
            $this->attr(...$attributes);
        }

        // opening:
        // < element/extends attributes> content </element/extends>
        $elementName = ($this->isWebComponent())
            ? $this->_extends
            : $this->getElementName();
        $attributes = $this->compiledAttributes();
        $opening = '<'. $elementName;
        if (strlen($attributes) > 0) {
            $opening .= ' '. $attributes;
        }
        $opening .= '>';

        $content = $this->compileContent();
        
        $closing = ($this->hasEndTag())
            ? '</'. $elementName .'>'
            : '';

        return $opening . $content . $closing;
    }

    private function getElementName()
    {
        return str_replace('_', '-', $this->_element);
    }

    private function compileContent()
    {
        $content = '';
        if ($this->isComponent($this->_content)) {
            $content = $this->_content->compile();

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

    private function compiledAttributes(): string
    {
        $prefixed = [];
        if ($this->isWebComponent()) {
            $prefixed[] = 'is '. $this->getElementName();
        }

        if (strlen($this->_role) > 0) {
            $prefixed[] = 'role '. $this->_role;
        }

        if (count($prefixed) > 0) {
            array_unshift($this->_attributes, ...$prefixed);    
        }
        
        $attributes = '';
        if (count($this->_attributes) > 0) {
            $preparedAttributes = [];
            foreach ($this->_attributes as $attribute) {
                list($key, $value) = explode(' ', $attribute, 2);
                $preparedAttributes[] = $key .'="'. $value .'"';
            }
            $attributes = implode(' ', $preparedAttributes);
        }

        if (count($this->_attributes) > 0) {
            
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