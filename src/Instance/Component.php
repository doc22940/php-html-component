<?php

namespace Eightfold\HtmlComponent\Instance;

use Eightfold\HtmlComponent\Interfaces\Compile;

use Eightfold\HtmlComponent\Traits\HasParent;

class Component implements Compile
{
    use HasParent;

    const openingFormat = "<%s%s>";

    const closingFormat = '</%s>';

    // JSON %s:%s
    // PHP assoc array '%s'=>'%s'
    const attributeFormat = '%s="%s"';

    protected $_element = '';
    protected $_extends = '';
    protected $_role = '';
    protected $_content;
    protected $_omitEndTag = false;
    protected $_attributes = [];
    
    /**
     * Return dictionary where index 0 is the continuous string appearing before a 
     * space in the full string.
     * 
     * @param  string $string [description]
     * @return [type]         [description]
     */
    protected static function splitFirstSpace(string $string): array
    {
        $return = explode(' ', $string, 2);
        return $return;
    }

    /**
     * Main element factory.
     * 
     * @param  string    $element    Name of the element to create.
     * @param  array     $attributes Array of attribute keys and values.
     * @param  [Compile] $content    One or more elements to place inside the elemnt.
     * 
     * @return Component             A Component instance with the specified element
     *                               name, content, and attributes.
     */
    public static function make(
        string $element, 
        array $attributes = [], 
        Compile ...$content)
    {
        $self = new static(...$content);
        
        $self->_element = $element;

        return $self->attr(...$attributes);
    }

    /**
     * Create instance with one or more Compile instances.
     * 
     * @param [type] $content [description]
     */
    protected function __construct(Compile ...$content)
    {
        $this->_content = $content;
    }

    public function omitEndTag(bool $omit = true): Component
    {
        $this->_omitEndTag = $omit;
        return $this;
    }

    /**
     * Build compoent string.
     * 
     * @param  [type] $attributes [description]
     * @return [type]             [description]
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
        if (strlen($attributes) > 0) {
            $attributes = ' '. $attributes;
        }

        $opening = sprintf(self::openingFormat, $elementName, $attributes);

        $closing = ($this->hasEndTag())
            ? sprintf(self::closingFormat, $elementName)
            : '';

        $content = $this->compileContent($this->_content);

        return $opening . $content . $closing;
    }

    /**
     * Use PHP print() to print the compiled string.
     * 
     * @param  string $attributes One or more strings where the key comes before the
     *                            first space. ex. `id some-id`
     *                            
     * @return int                The return of the print function.
     */
    public function print(string ...$attributes)
    {
        return print $this->compile(...$attributes);
    }

    /**
     * Use PHP echo() to print the compiled string.
     * 
     * @param string $attributes One or more strings where the key comes before the
     *                           first space. ex. `id some-id`
     */
    public function echo(string ...$attributes)
    {
        echo $this->compile(...$attributes);
    }

    /**
     * Get the element that was used to instantiate the component.
     * 
     * @return string Name of the element for the cmoponent.
     */
    public function getElement(): string
    {
        return $this->_element;
    }

    /**
     * The element this component extends, if applicable.
     * 
     * @param  string $extends The name of the element this component extends.
     * 
     * @return Component    The instance the method was called on.
     */
    public function extends(string $extends): Component
    {
        $this->_extends = $extends;
        return $this;
    }

    /**
     * Set the role of the component in the application.
     * 
     * @param  string $role Value for the component attribute.
     * 
     * @return Component    The instance the method was called on.
     */
    public function role(string $role): Component
    { 
        $this->_role = $role;
        return $this;
    } 

    /**
     * Set the attributes of the instance.
     * 
     * @param string $attributes One or more strings where the key comes before the
     *                           first space. ex. `id some-id`
     *                           
     * @return Component    The instance the method was called on.
     */
    public function attr(string ...$attributes): Component
    {
        foreach ($attributes as $attribute) {
            $this->addAttribute($attribute);
        }
        return $this;
    }

    /**
     * Convert string attributes to attribute dictionary.
     * 
     * @param string $attribute One string where the key comes before the first space.
     *                          ex. `id some-id`
     */
    private function addAttribute(string $attribute)
    {
        if (strlen($attribute) > 0) {
            list($key, $value) = self::splitFirstSpace($attribute);
            $this->_attributes[$key] = $value;            
        }
    }

    /**
     * Convert attributes array to string to place within the element.
     * 
     * @return string The aggregated string of attributes. ex. id="some-id".
     */
    private function compileAttributes(): string
    {
        $return = '';

        // Setup
        $prefixed = [];
        if ($this->isWebComponent()) {
            $prefixed['is'] = $this->getElementName();
        }

        if (strlen($this->_role) > 0) {
            $prefixed['role'] = $this->_role;
        }

        $attributes = $this->_attributes;
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

    /**
     * Convert element function name to valid web component string by replacing
     * underscores with hyphens.
     * 
     * @return string String after replacing undescores with hyphens.
     */
    private function getElementName(): string
    {
        return str_replace('_', '-', $this->_element);
    }

    /**
     * Recursively compile the content of the Component instance.
     * 
     * @param  array|Compile $contentToCompile One or more elements with the ability to
     *                                         compile.
     * 
     * @return string The compiled string for the component's content.
     */
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

    /**
     * Check if the component extends another web element or component.
     * 
     * @return boolean Whether the component extends another web element or component.
     */
    private function isWebComponent(): bool
    {
        return (strlen($this->_element) > 0 && strlen($this->_extends) > 0);
    }

    private function hasEndTag(): bool
    {
        return ( ! $this->_omitEndTag);
    }
}