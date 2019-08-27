<?php

namespace Eightfold\HtmlComponent;

use Eightfold\HtmlComponent\Instance\Component as InstanceComponent;
use Eightfold\HtmlComponent\Instance\Text;

abstract class Component
{
    static public function __callStatic(string $element, array $args)
    {
        if (self::isTextAndIsNotEmpty($element, $args)) {
            return Text::make($args[0]);
        }
        return InstanceComponent::make($element, [], ...$args);
    }

    static public function isTextAndIsNotEmpty(string $element, array $args): bool
    {
        return $element == 'text' && isset($args[0]);
    }
}