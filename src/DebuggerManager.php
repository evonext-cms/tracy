<?php
/*
 EvoNext CMS Tracy
 Copyright (c) 2022
 Licensed under MIT License
 */

namespace EvoNext\Tracy;

use ErrorException;
use EvoNext\Tracy\Session\DeferredContent;
use EvoNext\Tracy\Session\Session;
use Illuminate\Support\Arr;
use Throwable;
use Tracy\Bar;
use Tracy\BlueScreen;
use Tracy\Debugger;
use Tracy\Helpers;

class DebuggerManager
{
    private array      $config;
    private Bar        $bar;
    private BlueScreen $blueScreen;
    private Session    $session;
    private            $url;
    private            $defer;

    public static function init(array $config = [])
    {
        $config = array_merge([
            'accepts'       => [],
            'appendTo'      => 'body',
            'showBar'       => false,
            'editor'        => Debugger::$editor,
            'maxDepth'      => Debugger::$maxDepth,
            'maxLength'     => Debugger::$maxLength,
            'scream'        => true,
            'showLocation'  => true,
            'strictMode'    => true,
            'currentTime'   => array_key_exists('REQUEST_TIME_FLOAT', $_SERVER) ? $_SERVER['REQUEST_TIME_FLOAT'] : microtime(true),
            'editorMapping' => isset(Debugger::$editorMapping) === true ? Debugger::$editorMapping : [],
        ], $config);

        Debugger::$editor       = $config['editor'];
        Debugger::$maxDepth     = $config['maxDepth'];
        Debugger::$maxLength    = $config['maxLength'];
        Debugger::$scream       = $config['scream'];
        Debugger::$showLocation = $config['showLocation'];
        Debugger::$strictMode   = $config['strictMode'];
        Debugger::$time         = $config['currentTime'];

        if (isset(Debugger::$editorMapping) === true) {
            Debugger::$editorMapping = $config['editorMapping'];
        }

        return $config;
    }

    public function __construct(array $config, BlueScreen $blueScreen, Bar $bar, DeferredContent $defer, $url = null)
    {
        $this->config     = $config;
        $this->blueScreen = $blueScreen;
        $this->bar        = $bar;
        $this->defer      = $defer;
        $this->url        = $url;
    }

    public function enabled(): bool
    {
        return Arr::get($this->config, 'enabled', true) === true;
    }

    public function showBar(): bool
    {
        return Arr::get($this->config, 'showBar', true) === true;
    }

    public function accepts(): array
    {
        return Arr::get($this->config, 'accepts', []);
    }

    public function dispatchAssets(string $type): array
    {
        switch ($type) {
            case 'css':
            case 'js':
                $headers = [
                    'Content-Type'  => $type === 'css' ? 'text/css; charset=utf-8' : 'text/javascript; charset=utf-8',
                    'Cache-Control' => 'max-age=86400',
                ];
                $content = $this->renderBuffer(function () {
                    $this->defer->sendAssets();
                });
                break;
            default:
                $headers = ['Content-Type' => 'text/javascript; charset=utf-8'];
                $content = $this->dispatch();
        }

        return [array_merge($headers, ['Content-Length' => strlen($content)]), $content];
    }

    public function dispatch(): string
    {
        $this->defer->isAvailable();

        return $this->renderBuffer(function () {
            $this->defer->sendAssets();
        });
    }

    public function shutdownHandler(string $content, bool $ajax = false, ?int $error = null)
    {
        $error = $error ?: error_get_last();
        if (is_array($error) && in_array($error['type'], [
                E_ERROR,
                E_CORE_ERROR,
                E_COMPILE_ERROR,
                E_PARSE,
                E_RECOVERABLE_ERROR,
                E_USER_ERROR,
            ], true)) {
            return $this->exceptionHandler(
                Helpers::fixStack(new ErrorException($error['message'], 0, $error['type'], $error['file'], $error['line']))
            );
        }

        return array_reduce(['renderLoader', 'renderBar'], function ($content, $method) use ($ajax) {
            return $this->$method($content, $ajax);
        }, $content);
    }

    /** @noinspection PhpInternalEntityUsedInspection */
    public function exceptionHandler(Throwable $exception)
    {
        return $this->renderBuffer(function () use ($exception) {
            Helpers::improveException($exception);
            $this->blueScreen->render($exception);
        });
    }

    private function renderLoader(string $content, bool $ajax = false)
    {
        if ($ajax === true || $this->defer->isAvailable() === false) {
            return $content;
        }

        return $this->render($content, 'renderLoader', ['head', 'body']);
    }

    private function renderBar(string $content): string
    {
        $tag = Arr::get($this->config, 'appendTo', 'body');

        return $this->render($content, 'render', [$tag, 'body']);
    }

    private function render(string $content, string $method, array $appendTags = ['body']): string
    {
        $appendHtml = $this->renderBuffer(function () use ($method) {
            $requestUri = Arr::get($_SERVER, 'REQUEST_URI');
            Arr::set($_SERVER, 'REQUEST_URI', '');
            call_user_func([$this->bar, $method], $this->defer);
            Arr::set($_SERVER, 'REQUEST_URI', $requestUri);
        });

        $appendTags = array_unique($appendTags);

        foreach ($appendTags as $appendTag) {
            $pos = strripos($content, '</'.$appendTag.'>');

            if ($pos !== false) {
                return substr_replace($content, $appendHtml, $pos, 0);
            }
        }

        return $content.$appendHtml;
    }

    private function renderBuffer(callable $callback): string
    {
        ob_start();
        $callback();

        return $this->replacePath(ob_get_clean());
    }

    private function replacePath(string $content): string
    {
        return $this->url
            ? str_replace('?_tracy_bar', $this->url.'?_tracy_bar', $content)
            : $content;
    }
}
