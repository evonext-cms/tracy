<?php
/*
 EvoNext CMS Tracy
 Copyright (c) 2022
 Licensed under MIT License
 */

namespace EvoNext\Tracy;

use EvoNext\Tracy\Exceptions\Handler;
use EvoNext\Tracy\Exceptions\HandlerForLaravel6;
use EvoNext\Tracy\Middleware\RenderBar;
use EvoNext\Tracy\Session\DeferredContent;
use EvoNext\Tracy\Session\Session;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Contracts\View\Factory as View;
use Illuminate\Routing\Router;
use Illuminate\Support\Arr;
use Illuminate\Support\ServiceProvider;
use Tracy\Bar;
use Tracy\BlueScreen;
use Tracy\Debugger;

class TracyServiceProvider extends ServiceProvider
{
    protected bool   $defer     = false;
    protected string $namespace = 'EvoNext\Tracy\Http\Controllers';

    protected ?bool $isBackend  = null;
    protected ?bool $isTopFrame = null;

    /**
     * boot.
     *
     * @param Kernel $kernel
     * @param View $view
     * @param Router $router
     */
    public function boot(Kernel $kernel, View $view, Router $router)
    {
        $config = $this->app['config']->get('tracy');
        $this->handleRoutes($router, Arr::get($config, 'route', []));

        if ($this->app->runningInConsole() === true) {
            $this->publishes([__DIR__.'/../config/tracy.php' => config_path('tracy.php')], 'config');

            return;
        }

        /** @var \Illuminate\View\Engines\CompilerEngine $engine */
        $engine = $view->getEngineResolver()->resolve('blade');
        $engine->getCompiler()->directive('bdump', function ($expression) {
            return "<?php \Tracy\Debugger::barDump($expression); ?>";
        });

        $enabled = Arr::get($config, 'enabled', true) === true;
        if ($enabled === false) {
            return;
        }

        $showException = Arr::get($config, 'showException', true);
        if ($showException === true) {
            $this->app->extend(ExceptionHandler::class, function ($exceptionHandler, $app) {
                $debuggerManager = $app[DebuggerManager::class];

                return version_compare($this->app->version(), '7.0', '>=')
                    ? new Handler($exceptionHandler, $debuggerManager)
                    : new HandlerForLaravel6($exceptionHandler, $debuggerManager);
            });
        }

        $showBar = Arr::get($config, 'showBar', true);
        if ($showBar === true) {
            $kernel->prependMiddleware(RenderBar::class);
        }
    }

    /**
     * Register the service provider.
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/tracy.php', 'tracy');

        $this->app['config']->set([
            'tracy.enabled' => $this->checkEnabled(),
            'tracy.showBar' => $this->checkShowBar(),
        ]);

        $config = $this->app['config']->get('tracy');

        // if (Arr::get($config, 'panels.terminal') === true) {
        //     $this->app->register(TerminalServiceProvider::class);
        // }

        $this->app->bind(BlueScreen::class, function () {
            return Debugger::getBlueScreen();
        });

        $this->app->bind(Bar::class, function ($app) use ($config) {
            return (new BarManager(Debugger::getBar(), $app['request'], $app))
                ->loadPanels(Arr::get($config, 'panels', []))
                ->getBar();
        });

        $this->app->bind(DebuggerManager::class, function ($app) use ($config) {
            $config     = DebuggerManager::init($config);
            $blueScreen = $app[BlueScreen::class];
            $bar        = $app[Bar::class];
            $defer      = new DeferredContent($bar, new Session());
            $routeAs    = Arr::get($config, 'route.as');
            $url        = $routeAs ? $app['url']->route($routeAs.'bar') : null;

            return new DebuggerManager($config, $blueScreen, $bar, $defer, $url);
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [ExceptionHandler::class];
    }

    /**
     * register routes.
     *
     * @param Router $router
     * @param array $config
     */
    protected function handleRoutes(Router $router, array $config = [])
    {
        if ($this->app->routesAreCached() === false) {
            $router->group(array_merge([
                'namespace' => $this->namespace,
            ], $config), function () {
                require __DIR__.'/../routes/web.php';
            });
        }
    }

    protected function checkEnabled(): bool
    {
        $enabled = $this->app['config']->get('tracy.enabled');

        return ($enabled === true
            || ($enabled === 'manager' && $this->isBackend())
            || ($enabled === 'web') && $this->isFrontend());
    }

    protected function checkShowBar(): bool
    {
        $showBar = $this->app['config']->get('tracy.showBar');

        if ($showBar === false) return false;

        if ($this->isFrontend()) return $showBar;

        if ($this->app['config']->get('tracy.enabledInTopFrame')) return $this->isTopFrame();

        return $this->isPageFrame();
    }

    protected function isTopFrame(): bool
    {
        if (is_null($this->isTopFrame)) {
            if ($this->isFrontend()) return $this->isTopFrame = false;

            $managerPrefix   = $this->app['config']->get('tracy.managerPrefix');
            $managerTopRoute = $this->app['config']->get('tracy.managerTopRoute');

            $this->isTopFrame = preg_match('~^/'.$managerPrefix.'/'.$managerTopRoute.'~', $this->app['request']->getRequestUri());
        }

        return $this->isTopFrame;
    }

    protected function isPageFrame(): bool
    {
        if ($this->isFrontend()) return false;
        return !$this->isTopFrame();
    }

    protected function isBackend(): bool
    {
        if (is_null($this->isBackend)) {
            $managerPrefix   = $this->app['config']->get('tracy.managerPrefix');
            $this->isBackend = preg_match('~^/'.$managerPrefix.'~', $this->app['request']->getRequestUri());
        }

        return $this->isBackend;
    }

    protected function isFrontend(): bool
    {
        return !$this->isBackend();
    }
}
