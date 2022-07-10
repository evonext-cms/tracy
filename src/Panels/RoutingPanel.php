<?php
/*
 EvoNext CMS Tracy
 Copyright (c) 2022
 Licensed under MIT License
 */

namespace EvoNext\Tracy\Panels;

use EvoNext\Tracy\Contracts\IAjaxPanel;
use Illuminate\Support\Arr;

class RoutingPanel extends AbstractPanel implements IAjaxPanel
{
    /**
     * getAttributes.
     *
     * @return array
     */
    protected function getAttributes(): array
    {
        $rows = ['uri' => 404];
        if ($this->hasLaravel() === true) {
            $router       = $this->laravel['router'];
            $currentRoute = $router->getCurrentRoute();
            if (is_null($currentRoute) === false) {
                $rows = array_merge([
                    'uri' => $currentRoute->uri(),
                ], $currentRoute->getAction());
            }
        } else {
            $rows['uri'] = empty(Arr::get($_SERVER, 'HTTP_HOST')) === true ?
                404 : Arr::get($_SERVER, 'REQUEST_URI');
        }

        return compact('rows');
    }
}
