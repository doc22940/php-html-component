# 8fold Component

A featherweight class for generating predefined HTML or autonymous web components.

While developing the user interfaces for our various online applications we managed to consolidate the basic logic for generating HTML elements, web components, and complete pages of content. This consolidation resulted in the creation of a single featherweight class we call 8fold Component, available on [Packagist](https://packagist.org/packages/8fold/php-html-component) and [GitHub](https://github.com/8fold/php-html-component).

It is the backbone for our other web user interface packages as well as all our websites.

## Composer

```
$ composer require 8fold/php-html-component
```

## Usage

Make a static function call to the Component class. The name of the function will most likely become the name of the component (because function names should not contain hyphens, use underscores instead). The first argument will be its content; you can set this to `false` for self-closing elements. The second argument is optional and will become an extension.

Note: 8fold Component is strictly typed. Therefore, all content passed into 8fold Component must implement the Compile interface, which means a string literal will fail unless instantiated as `Component::text`.

```php
Component::my_component(
    Component::text('Hello, World!')
  )->compile();
```

Output:

```html
<my-component>
  Hello, World!
</my-component>
```

Extending a known element:

```php
Component::my_component(
    Component::text('Hello, World!')
  )->extends('p')
  ->compile();
```

Output:

```html
<p is="my-component">
  Hello, World!
</p>
```

Adding attributes:

```php
Component::my_component(
    Component::text('Hello, World!')
  )->extends('p')
  ->attr('id something-unique')
  ->compile();
```

Output:

```html
<p is="my-component" id="something-unique">
  Hello, World!
</p>
```


The only opinion made by the Component class is the order of the primary identifying attributes for the element, in this order:

1. element,
2. extension (`is`),
3. role.

After that, the attributes will appear in the order in which they are received. The content will then be processed. Then capped off with a closing tag, if applicable.

## Extending

The Component class is designed to be extended in order to reduce complexity in order to create more compound elements and components. It is recommended you extend both `Component.php` (using it as a factory) and `Instance/Component.php` (using it as a base class). The root Component class is essentially a factory (could be viewed as a facade as well), which creates and returns a Component instance with the given specifications. 

When creating an extension, we reccomend duplicating root `Component` entry point, then creating a list of valid elements for your extension and calling parent if it's not available (we found it to be the easiest way to handle inheritance and the magic methods). This way developers using your extension will always get a Component back.

```php
// Eightfold\HtmlComponent\Component
abstract class Component
{
  public static function __callStatic(string $element, array $args)
  {
    ...
  }
}

// My\ComponentExtension\IsAwesome
abstract class Component
{
  public static function __callStatic(string $element, array $args)
  {
    $validElements = [
      'my_component'
    ];

    if (in_array($element, $validElements)) {
        ...

        return ...;
    }
    return parent::$element($args);
  }
}
```

Then you can extend `Instance/Component.php` as you normally would, using your `Component` factory to return instances of your own classes, that can leverage the `Instance\Component`. Chances are the method you will override from `Instance\Component` is the `compile` method.

```php
// My\ComponentExtension\Instance\IsAwesome

use Eightfold\HtmlComponent\Instance\Component;

abstract class IsAwesome extends Component
{
  public function compile(...$attributes): string
  {
    ...
  }
}
```

But you do what you need to! And maybe let's know about it.