<?php

namespace EvoNext\Tracy\Tests;

use EvoNext\Tracy\DebuggerManager;
use EvoNext\Tracy\Exceptions\Handler;
use EvoNext\Tracy\Exceptions\HandlerForLaravel6;
use EvoNext\Tracy\Middleware\RenderBar;
use EvoNext\Tracy\TracyServiceProvider;
use Illuminate\Config\Repository;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Contracts\View\Factory;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Routing\Router;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;
use Recca0120\Terminal\TerminalServiceProvider;
use Tracy\BlueScreen;

class TracyServiceProviderTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    public function testRegister()
    {
        $app    = m::spy(new Application());
        $config = new Repository();
        $app->instance('config', $config);
        $config->set('tracy', ['panels' => ['terminal' => true]]);
        $serviceProvider = new TracyServiceProvider($app);

        $serviceProvider->register();

        $app->shouldHaveReceived('register')->with(TerminalServiceProvider::class)->once();
        $app->shouldHaveReceived('bind')->with(BlueScreen::class, m::type('Closure'));
        $app->shouldHaveReceived('bind')->with(DebuggerManager::class, m::type('Closure'));
    }

    public function testBoot()
    {
        $app    = m::spy(new Application());
        $config = new Repository();
        $app->instance('request', Request::capture());
        $app->instance('config', $config);
        $config->set('tracy', [
            'enabled' => true,
            'route'   => ['prefix' => 'laravel-tracy', 'showException' => true],
        ]);
        $serviceProvider = new TracyServiceProvider($app);

        $app->expects('routesAreCached')->andReturns(false);
        $app->expects('runningInConsole')->andReturns(false);

        $view = m::spy(Factory::class);
        $view->expects('getEngineResolver')->andReturnSelf();
        $view->expects('resolve')->andReturnSelf();
        $view->expects('getCompiler')->andReturnSelf();

        $kernel = m::spy(Kernel::class);
        $router = m::spy(Router::class);

        $serviceProvider->register();
        $serviceProvider->boot($kernel, $view, $router);

        $view->shouldHaveReceived('directive')->with('bdump', m::on(function ($closure) {
            $expression = '$foo';

            return $closure($expression) === "<?php \Tracy\Debugger::barDump({$expression}); ?>";
        }))->once();

        $app->shouldHaveReceived('extend')
            ->with(ExceptionHandler::class, m::on(function ($closure) use ($app) {
                $handler = $closure(m::spy(ExceptionHandler::class), $app);

                return $handler instanceof Handler || $handler instanceof HandlerForLaravel6;
            }))->once();
        $kernel->shouldHaveReceived('prependMiddleware')->with(RenderBar::class);
        $router->shouldHaveReceived('group')->with(array_merge([
            'namespace' => 'EvoNext\Tracy\Http\Controllers',
        ], $config['tracy']['route']), m::type('Closure'));
    }

    public function testBootRunningInConsole()
    {
        $app                = m::spy(new Application());
        $app['path.config'] = '';

        $config = new Repository();
        $config->set('tracy', ['panels' => ['terminal' => true]]);
        $app->instance('config', $config);

        $serviceProvider = new TracyServiceProvider($app);

        $app->expects('routesAreCached')->andReturns(false);
        $app->expects('runningInConsole')->andReturns(true);

        $kernel = m::spy(Kernel::class);
        $view   = m::spy(Factory::class);
        $router = m::spy(Router::class);

        $serviceProvider->boot($kernel, $view, $router);
    }

    public function testProviders()
    {
        $app    = m::spy(new Application());
        $config = new Repository();
        $app->instance('config', $config);
        $config->set('tracy', ['panels' => ['terminal' => true]]);
        $serviceProvider = new TracyServiceProvider($app);

        $this->assertSame([ExceptionHandler::class], $serviceProvider->provides());
    }
}
