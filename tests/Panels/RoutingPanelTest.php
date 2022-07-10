<?php

namespace EvoNext\Tracy\Tests\Panels;

use EvoNext\Tracy\Panels\RoutingPanel;
use EvoNext\Tracy\Template;
use Illuminate\Contracts\Routing\Registrar;
use Illuminate\Foundation\Application;
use Illuminate\Routing\Route;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;

class RoutingPanelTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    public function testRender()
    {
        $router = m::mock(Registrar::class);
        $currentRoute = m::spy(Route::class);
        $router->expects('getCurrentRoute')->andReturns($currentRoute);
        $currentRoute->expects('uri')->andReturns($uri = 'foo');
        $currentRoute->expects('getAction')->andReturns($action = ['foo' => 'bar']);

        $laravel = m::mock(new Application());
        $laravel['router'] = $router;

        $template = m::spy(new Template());
        $panel = new RoutingPanel($template);
        $panel->setLaravel($laravel);

        $template->expects('setAttributes')->with(['rows' => array_merge(['uri' => $uri], $action)]);
        $template->expects('render')->twice()->with(m::type('string'))->andReturns($content = 'foo');

        $this->assertSame($content, $panel->getTab());
        $this->assertSame($content, $panel->getPanel());
    }

    /**
     * @runTestsInSeparateProcesses
     */
    public function testRenderNative()
    {
        $_SERVER['HTTP_HOST'] = '127.0.0.1';
        $_SERVER['REQUEST_URI'] = '/foo';
        $template = m::spy(new Template());
        $panel = new RoutingPanel($template);

        $template->expects('setAttributes')->with(['rows' => ['uri' => $_SERVER['REQUEST_URI']]]);
        $template->expects('render')->twice()->with(m::type('string'))->andReturns($content = 'foo');

        $this->assertSame($content, $panel->getTab());
        $this->assertSame($content, $panel->getPanel());
    }
}
