# HTML Component by 8fold in PHP

A fetherweight class for generating predefined HTML or autonymous web components.

While developing [8fold UI](https://ui.8fold.software) we decided to rewrite [PHP HTML](https://github.com/8fold/php-html). In doing so, we concetrated the heavy lifting into a single class and decided to make it into its own package.

## Composer

```
$ composer require 8fold/php-html-component
```

## Usage

There is only one public method, `build`. It accepts a configuration array.

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
<my-component id="something-unique" clas="my awesome styles">Hello, World!</my-component>
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

The Component class only understand the following keys:

 Required keys
 
 - **element:** The string to place in the opening and closing tags. 
                Ex. <html></html> or <my-component></my-component>
 
 Optional keys
 
 - **extends:**          Whether this element extends another, known element. Ex.
                         <button is="my-component"></button>
 - **role:**             The role the component plays in the document or application.
                         Ex. <body role="application">
 - **omit-closing-tag:** true|false (Default is false.) Whether the element has a
                         closing tag. Ex. <html></html> versus <img>
 - **attributes:**       Dictionary of key value pairs to place attributes inside the
                         element. Ex. <html lang="en"></html>
 - **content:**          string|array Accepts a single string or an array of 
                         component configurations.