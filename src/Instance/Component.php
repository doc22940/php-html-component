<?php

namespace Eightfold\HtmlComponent\Instance;

use Eightfold\HtmlComponent\Interfaces\Compile;

use Eightfold\HtmlComponent\Traits\HasParent;

class Component implements Compile
{
    use HasParent;

    const openingFormat = "<%s%s>";

    const closingFormat = '</%s>';

    const attributeFormat = '%s="%s"';

    protected $element = '';

    protected $extends = '';
    
    protected $role = '';
    
    protected $content;
    
    protected $omitEndTag = false;
    
    protected $attributes = [];

    static public function make(string $element, array $attributes = [], Compile ...$content)
    {
        $self = new static(...$content);
        
        $self->element = $element;

        return $self->attr(...$attributes);
    }

    static protected function splitFirstSpace(string $string): array
    {
        // return array where 
        // [0] is string before first space and
        // [1] is string after first space
        return explode(' ', $string, 2);
    }

    protected function __construct(Compile ...$content)
    {
        $this->content = $content;
    }

    public function omitEndTag(bool $omit = true): Component
    {
        $this->omitEndTag = $omit;
        return $this;
    }

    private function hasEndTag(): bool
    {
        return ( ! $this->omitEndTag);
    }

    public function getElement(): string
    {
        return $this->element;
    }

    private function getElementName(): string
    {
        return str_replace('_', '-', $this->element);
    }

    public function extends(string $extends): Component
    {
        $this->extends = $extends;
        return $this;
    }

    private function isWebComponent(): bool
    {
        return (strlen($this->element) > 0 && strlen($this->extends) > 0);
    }

    public function role(string $role): Component
    { 
        $this->role = $role;
        return $this;
    } 

    public function attr(string ...$attributes): Component
    {
        foreach ($attributes as $attribute) {
            $this->addAttribute($attribute);
        }
        return $this;
    }

    private function addAttribute(string $attribute)
    {
        if (strlen($attribute) > 0) {
            list($key, $value) = self::splitFirstSpace($attribute);
            $this->attributes[$key] = $value;            
        }
    }

    public function compile(string ...$attributes): string
    {
        if (count($attributes) > 0) {
            $this->attr(...$attributes);
        }

        // opening:
        // <element/extends attributes> content </element/extends>
        $elementName = ($this->isWebComponent())
            ? $this->extends
            : $this->getElementName();

        $attributes = $this->compileAttributes();
        if (strlen($attributes) > 0) {
            $attributes = ' '. $attributes;
        }

        $opening = sprintf(self::openingFormat, $elementName, $attributes);

        $closing = ($this->hasEndTag())
            ? sprintf(self::closingFormat, $elementName)
            : '';

        $content = $this->compileContent($this->content);

        return $opening . $content . $closing;
    }
    
    private function compileAttributes(): string
    {
        $return = '';

        // Setup
        $prefixed = [];
        if ($this->isWebComponent()) {
            $prefixed['is'] = $this->getElementName();
        }

        if (strlen($this->role) > 0) {
            $prefixed['role'] = $this->role;
        }

        $attributes = $this->attributes;
        if (count($prefixed) > 0) {
            $attributes = array_merge($prefixed, $attributes);
        }

        // Execute
        if (count($attributes) > 0) {
            $string = [];
            foreach ($attributes as $key => $value) {
                if ($key == $value && strlen($value) > 0) {
                    $string[] = $value;

                } else {
                    $string[] = sprintf(self::attributeFormat, $key, $value);

                }
            }
            $return = implode(' ', $string);
        }
        return $return;
    }

    private function compileContent($contentToCompile): string
    {
        $content = '';
        if ($contentToCompile instanceof Compile) {
            $content = $contentToCompile->parent($this)->compile();

        } elseif (is_array($contentToCompile)) {
            foreach ($contentToCompile as $maker) {
                $content .= $this->compileContent($maker);

            }
        }  
        return $content;
    }

    private function compileAttributes(): string
    {
        $return = '';

        // Setup
        $prefixed = [];
        if ($this->isWebComponent()) {
            $prefixed['is'] = $this->getElementName();
        }

        if (strlen($this->role) > 0) {
            $prefixed['role'] = $this->role;
        }

        $attributes = $this->attributes;
        if (count($prefixed) > 0) {
            $attributes = array_merge($prefixed, $attributes);
        }

        // Execute
        if (count($attributes) > 0) {
            $string = [];
            foreach ($attributes as $key => $value) {
                if ($key == $value && strlen($value) > 0) {
                    $string[] = $value;

                } else {
                    $string[] = sprintf(self::attributeFormat, $key, $value);

                }
            }
            $return = implode(' ', $string);
        }
        return $return;
    }

    private function compileContent($contentToCompile): string
    {
        $content = '';
        if ($contentToCompile instanceof Compile) {
            $content = $contentToCompile->parent($this)->compile();

        } elseif (is_array($contentToCompile)) {
            foreach ($contentToCompile as $maker) {
                $content .= $this->compileContent($maker);

            }
        }  
        return $content;
    }

    public function print(string ...$attributes)
    {
        return print $this->compile(...$attributes);
    }

    public function echo(string ...$attributes)
    {
        echo $this->compile(...$attributes);
    }    
}