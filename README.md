# 8fold Component

A fetherweight class for generating predefined HTML or autonymous web components.

While developing the user interfaces for our various online applications we managed to consolidate the basic logic for generating HTML elements, web components, and complete pages of content. This consolidation resulted in the creation of a single featherweight class we call Component by 8fold, which is available via Composer and [Packagist](https://packagist.org/packages/8fold/php-html-component) and [GitHub](https://github.com/8fold/php-html-component).

It is the backbone for our other web user interface packages.

## Composer

```
$ composer require 8fold/php-html-component
```

## Usage

There is only one public method, `build`. It accepts a configuration dictionary.

```php
Eightfold\HtmlComponent\Component::build([
    'element' => 'my-component',
    'content' => 'Hello, World!',
    'attributes' => [
        'id' => 'something-unique',
        'class' => 'my awesome styles'
    ]
]);
```

Output:

```html
<my-component id="something-unique" clas="my awesome styles">
    Hello, World!
</my-component>
```

Extending a known element:

```php
Eightfold\HtmlComponent\Component::build([
    'element' => 'my-component',
    'extends' => 'p',
    'content' => 'Hello, World!'
]);
```

Output:

```html
<p is="my-component">Hello, World!</p>
```

The Component class only understands the following keys:

 Required key
 
 - **element:** The string to place in the opening and closing tags. 
                Ex. `<html></html>` or `<my-component></my-component>`
 
 Optional keys
 
 - **extends:**          Whether this element extends another, known element. Ex.
                         `<button is="my-component"></button>`
 - **role:**             The role the component plays in the document or application.
                         Ex. `<body role="application">`
 - **omit-closing-tag:** true|false (Default is false.) Whether the element has a
                         closing tag. Ex. `<html></html>` versus `<img>`
 - **attributes:**       Dictionary of key value pairs to place attributes inside the
                         element. Ex. `<html lang="en"></html>`
 - **content:**          string|array Accepts a single string or an array of 
                         component configurations.

The only opinions made by the Component class is the order of the primary identifying attributes, in this order:

1. element,
2. extension (is),
3. role.

After that, the attributes will appear in the order in which they are received. The content will then be processed. Then capped off with a closing tag, if applicable.

## Extending

The Component class is designed to be extended in order to reduce the complexity needed by developers to create more compound elements and components. There are two override points:

1. The `build` method itself. This method calls three methods in turn to generate a complete HTML string: opening, closing, and content.
2. The `content` method is the second override point. This method focuses on the `content` key of the configuration. If the value is an array, it will call the `build` method.

Therefore, it is recommended that you use your `build` override to clean up the configuration any way you see fit, then call `parent::`. Further, if you are creating a complex library of elements and components, it is recommended that you override the `content` method as well, and most likely **not** call `parent::`. See 8fold Elements for a production implementation.