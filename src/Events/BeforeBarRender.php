<?php
/*
 EvoNext CMS Tracy
 Copyright (c) 2022
 Licensed under MIT License
 */

namespace EvoNext\Tracy\Events;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BeforeBarRender
{
    public Request  $request;
    public Response $response;

    public function __construct(Request $request, Response $response)
    {
        $this->request  = $request;
        $this->response = $response;
    }
}
