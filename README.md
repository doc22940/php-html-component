# HTML Component by 8fold in PHP

A fetherweight class for generating predefined HTML or autonymous web components.

While developing [8fold UI](https://ui.8fold.software) we decided to rewrite [PHP HTML](https://github.com/8fold/php-html). In doing so, we concetrated the heavy lifting into a single class and decided to make it into its own package.

## Composer

```
$ composer require 8fold/php-html-component
```

## Usage

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