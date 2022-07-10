<?php
/*
 EvoNext CMS Tracy
 Copyright (c) 2022
 Licensed under MIT License
 */

namespace EvoNext\Tracy\Session;

use Tracy\Bar;

class DeferredContent
{
    private Bar     $bar;
    private Session $session;

    public function __construct(Bar $bar, Session $session)
    {
        $this->bar     = $bar;
        $this->session = $session;
    }

    public function isAvailable()
    {
        if (headers_sent()) {
            return $this->session->isStarted();
        }

        if (!$this->session->isStarted()) {
            $this->session->start();
        }

        return $this->session->isStarted();
    }

    /** @noinspection PhpInternalEntityUsedInspection */
    public function sendAssets()
    {
        $this->bar->dispatchAssets();
    }
}
