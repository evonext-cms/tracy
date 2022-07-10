<?php
/*
 EvoNext CMS Tracy
 Copyright (c) 2022
 Licensed under MIT License
 */

namespace EvoNext\Tracy\Panels;

use EvoNext\Tracy\Contracts\IAjaxPanel;
use Illuminate\Http\Request;

class RequestPanel extends AbstractPanel implements IAjaxPanel
{
    /**
     * getAttributes.
     *
     * @return array
     */
    protected function getAttributes(): array
    {
        $request = $this->hasLaravel() === true ? $this->laravel['request'] : Request::capture();
        $rows    = [
            'ip'      => $request->ip(),
            'ips'     => $request->ips(),
            'query'   => $request->query(),
            'request' => $request->all(),
            'file'    => $request->file(),
            'cookies' => $request->cookie(),
            'format'  => $request->format(),
            'path'    => $request->path(),
            'server'  => $request->server(),
            // 'headers' => $request->header(),
        ];

        return compact('rows');
    }
}
