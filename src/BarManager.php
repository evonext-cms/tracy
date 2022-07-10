<?php
/*
 EvoNext CMS Tracy
 Copyright (c) 2022
 Licensed under MIT License
 */

namespace EvoNext\Tracy;

use EvoNext\Tracy\Contracts\IAjaxPanel;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Tracy\Bar;
use Tracy\Debugger;
use Tracy\IBarPanel;

class BarManager
{
    private array        $panels = [];
    private Bar          $bar;
    private Request      $request;
    private ?Application $app;

    public function __construct(Bar $bar = null, Request $request = null, Application $app = null)
    {
        $this->bar     = $bar ?: Debugger::getBar();
        $this->request = $request ?: Request::capture();
        $this->app     = $app;
    }

    public function getBar()
    {
        return $this->bar;
    }

    public function loadPanels(array $panels = [])
    {
        if (isset($panels['user']) === true) {
            $panels['auth'] = $panels['user'];
            unset($panels['user']);
        }

        $ajax = $this->request->ajax();

        foreach ($panels as $id => $enabled) {
            if ($enabled === false) {
                continue;
            }

            if ($ajax === true && $this->isAjaxPanel($id) === false) {
                continue;
            }
            $panel = static::make($id);
            $this->set($panel, $id);
        }

        return $this;
    }

    public function set(IBarPanel $panel, $id)
    {
        $panel->setLaravel($this->app);
        $this->panels[$id] = $panel;
        $this->bar->addPanel($panel, $id);

        return $this;
    }

    public function get($id)
    {
        return Arr::get($this->panels, $id);
    }

    private function isAjaxPanel($id): bool
    {
        return is_subclass_of(static::name($id), IAjaxPanel::class) === true;
    }

    private static function make($id)
    {
        $className = static::name($id);

        return new $className(new Template());
    }

    private static function name($id)
    {
        return '\\'.__NAMESPACE__.'\Panels\\'.Str::studly($id).'Panel';
    }
}
