<?php

namespace Eightfold\HtmlComponent\Traits;

use Eightfold\HtmlComponent\Instance\Component;

use Eightfold\HtmlComponent\Interfaces\Compile;

trait HasParent
{
    protected $parent = null;

    protected function getParent(): ?Component
    {
        return $this->parent;
    }

    public function parent(Compile $component)
    {
        $this->parent = $component;
        return $this;
    }
}