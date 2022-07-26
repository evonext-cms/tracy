<?php
/*
 EvoNext CMS Tracy
 Copyright (c) 2022
 Licensed under MIT License
 */

namespace EvoNext\Tracy\Panels;

use EvoNext\Tracy\Contracts\IAjaxPanel;

class SessionPanel extends AbstractPanel implements IAjaxPanel
{
    /**
     * getAttributes.
     *
     * @return array
     */
    protected function getAttributes(): array
    {
        $rows = [];
        if ($this->hasLaravel() === true) {
            $session = $this->laravel['session'];
            $rows    = [
                'sessionId'      => $session->getId(),
                'sessionConfig'  => $session->getSessionConfig(),
                'laravelSession' => $session->all(),
            ];
        }

        if (session_status() === PHP_SESSION_ACTIVE) {
            $rows['nativeSessionId'] = session_id();
            $rows['nativeSession']   = $_SESSION;
        }

        return compact('rows');
    }
}
