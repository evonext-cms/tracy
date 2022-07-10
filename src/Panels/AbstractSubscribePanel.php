<?php
/*
 EvoNext CMS Tracy
 Copyright (c) 2022
 Licensed under MIT License
 */

namespace EvoNext\Tracy\Panels;

use Illuminate\Contracts\Foundation\Application;

abstract class AbstractSubscribePanel extends AbstractPanel
{
    /**
     * @param Application|null $laravel
     * @return $this
     */
    public function setLaravel(Application $laravel = null)
    {
        parent::setLaravel($laravel);
        if ($this->hasLaravel() === true) {
            $this->subscribe();
        }

        return $this;
    }

    abstract protected function subscribe();
}
