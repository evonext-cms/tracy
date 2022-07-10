<?php
/*
 EvoNext CMS Tracy
 Copyright (c) 2022
 Licensed under MIT License
 */

namespace EvoNext\Tracy\Panels;

use EvoNext\Tracy\Contracts\ILaravelPanel;
use EvoNext\Tracy\Template;
use Illuminate\Contracts\Foundation\Application;
use Tracy\Helpers;
use Tracy\IBarPanel;

abstract class AbstractPanel implements IBarPanel, ILaravelPanel
{
    protected Template    $template;
    protected Application $laravel;

    /** @var mixed */
    private array   $attributes;
    private ?string $viewPath = null;

    public function __construct(Template $template = null)
    {
        $this->template = $template ?: new Template();
    }

    /**
     * setLaravel.
     *
     * @param Application|null $laravel
     * @return $this
     */
    public function setLaravel(Application $laravel = null)
    {
        if (is_null($laravel) === false) {
            $this->laravel = $laravel;
        }

        return $this;
    }

    /**
     * Renders HTML code for custom tab.
     *
     * @return string
     */
    public function getTab()
    {
        return $this->render('tab');
    }

    /**
     * Renders HTML code for custom panel.
     *
     * @return string
     */
    public function getPanel()
    {
        return $this->render('panel');
    }

    /**
     * has laravel.
     *
     * @return bool
     */
    protected function hasLaravel()
    {
        return is_a($this->laravel, Application::class);
    }

    /**
     * render.
     *
     * @param string $view
     * @return string
     */
    protected function render(string $view)
    {
        $view = $this->getViewPath().$view.'.php';
        if (empty($this->attributes) === true) {
            $this->template->setAttributes(
                $this->attributes = $this->getAttributes()
            );
        }

        return $this->template->render($view);
    }

    abstract protected function getAttributes(): array;

    /**
     * getViewPath.
     *
     * @return string
     */
    private function getViewPath()
    {
        if (is_null($this->viewPath) === false) {
            return $this->viewPath;
        }

        return $this->viewPath = __DIR__.'/../../resources/views/'.ucfirst(class_basename(get_class($this))).'/';
    }

    /**
     * Use a backtrace to search for the origin of the query.
     *
     * @return string|array
     */
    protected static function findSource()
    {
        $source = '';
        $trace  = debug_backtrace(PHP_VERSION_ID >= 50306 ? DEBUG_BACKTRACE_IGNORE_ARGS : false);
        foreach ($trace as $row) {
            if (isset($row['file']) === false) {
                continue;
            }

            if (isset($row['function']) === true && strpos($row['function'], 'call_user_func') === 0) {
                continue;
            }

            if (isset($row['class']) === true && (
                    is_subclass_of($row['class'], IBarPanel::class) === true ||
                    strpos(str_replace('/', '\\', $row['file']), 'Illuminate\\') !== false
                )) {
                continue;
            }

            $source = [$row['file'], (int)$row['line']];
        }

        return $source;
    }

    /**
     * editor link.
     *
     * @param string|array $source
     * @return string
     */
    protected static function editorLink($source)
    {
        if (is_string($source) === true) {
            $file = $source;
            $line = null;
        } else {
            [$file, $line] = $source;
        }

        return Helpers::editorLink($file, $line);
    }
}
