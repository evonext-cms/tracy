<?php

namespace EvoNext\Tracy\Contracts;

use Illuminate\Contracts\Foundation\Application;

interface ILaravelPanel
{
    /**
     * setLaravel.
     *
     * @param \Illuminate\Contracts\Foundation\Application $laravel
     * @return static
     */
    public function setLaravel(Application $laravel = null);
}
