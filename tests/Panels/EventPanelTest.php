<?php

namespace EvoNext\Tracy\Tests\Panels;

use EvoNext\Tracy\Panels\EventPanel;
use EvoNext\Tracy\Template;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Foundation\Application;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;

class EventPanelTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    public function testRender()
    {
        $template = m::spy(new Template());
        $panel = new EventPanel($template);
        $events = m::spy(Dispatcher::class);
        $laravel = m::spy(new Application());
        $laravel['events'] = $events;
        $laravel->expects('version')->andReturns(5.4);
        $events->expects('listen')->with('*', m::on(function ($closure) {
            $closure('foo', ['foo' => 'bar']);

            return true;
        }));
        $panel->setLaravel($laravel);

        $template->expects('setAttributes')->with(m::type('array'));
        $template->expects('render')->twice()->with(m::type('string'))->andReturns($content = 'foo');

        $this->assertSame($content, $panel->getTab());
        $this->assertSame($content, $panel->getPanel());
    }

    public function testRenderAndLaravel53()
    {
        $laravel = m::spy(new Application());
        $laravel->expects('version')->andReturns(5.3);

        $events = m::spy(Dispatcher::class);
        $laravel['events'] = $events;
        $events->expects('firing')->andReturns('foo');
        $events->expects('listen')->with('*', m::on(function ($closure) {
            $closure(['foo' => 'bar']);

            return true;
        }));

        $template = m::spy(new Template());
        $panel = new EventPanel($template);
        $panel->setLaravel($laravel);

        $template->expects('setAttributes')->with(m::type('array'));
        $template->expects('render')->twice()->with(m::type('string'))->andReturns($content = 'foo');

        $this->assertSame($content, $panel->getTab());
        $this->assertSame($content, $panel->getPanel());
    }
}
