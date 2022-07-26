<?php
/*
 EvoNext CMS Tracy
 Copyright (c) 2022
 Licensed under MIT License
 */

namespace EvoNext\Tracy\Panels;

use Exception;
use Recca0120\Terminal\Http\Controllers\TerminalController;

class TerminalPanel extends AbstractPanel
{
    /**
     * Renders HTML code for custom panel.
     *
     * @return string
     */
    public function getPanel()
    {
        $this->template->minify(false);

        return $this->render('panel');
    }

    /**
     * getAttributes.
     *
     * @return array
     */
    protected function getAttributes(): array
    {
        $terminal = null;
        if ($this->hasLaravel() === true) {
            try {
                $controller = $this->laravel->make(TerminalController::class);
                $response   = $this->laravel->call([$controller, 'index'], ['view' => 'panel']);
                $terminal   = $response->getContent();
            } catch (Exception $e) {
                $terminal = $e->getMessage();
            }
        }

        return [
            'terminal' => $terminal,
        ];
    }
}
