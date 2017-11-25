<?php

namespace Eightfold\HtmlComponent\Traits;

use Eightfold\HtmlComponent\Interfaces\Compile;

trait HasParent
{
    protected $_parent = null;

    protected function getParent(): ?Component
    {
        return $this->_parent;
    }

    public function parent(Compile $component)
    {
        $this->_parent = $component;
        return $this;
    }
}