<?php
/*
 EvoNext CMS Tracy
 Copyright (c) 2022
 Licensed under MIT License
 */

namespace EvoNext\Tracy;

class Template
{
    private array $attributes = [];
    private bool  $minify     = true;

    public function setAttributes(array $attributes)
    {
        $this->attributes = $attributes;

        return $this;
    }

    public function minify(bool $minify)
    {
        $this->minify = $minify;

        return $this;
    }

    public function render(string $view): string
    {
        extract($this->attributes);

        ob_start();
        require $view;

        return $this->minify === true
            ? $this->min(ob_get_clean())
            : ob_get_clean();
    }

    /**
     * if need min style and script, refrence
     * https://gist.github.com/recca0120/5930842de4e0a43a48b8bf027ab058f9
     *
     * @param string $buffer
     * @return string
     */
    private function min(string $buffer): string
    {
        return preg_replace(
            ['/<!--(.*)-->/Uis', '/[[:blank:]]+/'],
            ['', ' '],
            str_replace(["\n", "\r", "\t"], '', $buffer)
        );
    }
}
