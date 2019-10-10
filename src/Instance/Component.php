<?php

namespace Eightfold\HtmlComponent\Instance;

use Eightfold\HtmlComponent\Interfaces\Compile;

use Eightfold\HtmlComponent\Traits\HasParent;

class Component implements Compile
{
    use HasParent;

    const openingFormat = "<%s%s>";

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

    public function __construct(Compile ...$content)
    {
        $this->content = $content;
    }

    public function omitEndTag(bool $omit = true): Component
    {
        $this->omitEndTag = $omit;
        return $this;
    }

    public function getElement(): string
    {
        return $this->element;
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
            if (strlen($attribute) > 0) {
                // return array where
                // [0] is string before first space and
                // [1] is string after first space
                list($key, $value) = $this->splitFirstSpace($attribute);
                $this->attributes[$key] = $value;
            }
        }
        return $this;
    }

    protected function splitFirstSpace(string $attribute): array
    {
        return explode(' ', $attribute, 2);
    }

    // unfold
    public function compile(string ...$attributes): string
    {
        $this->attr(...$attributes);

        $elementName = str_replace('_', '-', $this->element);
        if ($this->isWebComponent()) {
            $this->attr("is {$elementName}");
            $elementName = $this->extends;
        }

        if (strlen($this->role) > 0) {
            $this->attr("role {$this->role}");
        }

        $attributes = $this->compileAttributes();
        if (strlen($attributes) > 0) {
            $attributes = ' '. $attributes;
        }

        $opening = "<{$elementName}{$attributes}>";

        $closing = ($this->hasEndTag())
            ? "</{$elementName}>"
            : '';

        $content = $this->compileContent($this->content);

        return $opening . $content . $closing;
    }

    private function hasEndTag(): bool
    {
        return ( ! $this->omitEndTag);
    }

    private function compileAttributes(): string
    {
        $string = [];
        foreach ($this->attributes as $key => $value) {
            if ($key == $value && strlen($value) > 0) {
                // required=required => required
                $string[] = $value;

            } else {
                $string[] = "{$key}=\"{$value}\"";

            }
        }
        return implode(' ', $string);
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

    public function __toString()
    {
        return $this->compile();
    }
}
