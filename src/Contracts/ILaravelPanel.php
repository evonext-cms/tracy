<?php
/*
 EvoNext CMS Tracy
 Copyright (c) 2022
 Licensed under MIT License
 */

namespace EvoNext\Tracy\Contracts;

use Illuminate\Contracts\Foundation\Application;

interface ILaravelPanel
{
    /**
     * setLaravel.
     *
     * @param \Illuminate\Contracts\Foundation\Application|null $laravel
     * @return static
     */
    public function setLaravel(Application $laravel = null);
}
