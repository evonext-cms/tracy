<?php

namespace EvoNext\Tracy\Tests;

use EvoNext\Tracy\BarManager;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;
use Tracy\Bar;
use Tracy\IBarPanel;

class BarManagerTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    public function testLoadPanels()
    {
        $bar = m::spy(new Bar());
        $request = m::spy(Request::capture());
        $barManager = new BarManager($bar, $request, new Application());

        $request->expects('ajax')->andReturns(true);

        $barManager->loadPanels(['user' => true, 'terminal' => true]);

        $this->assertSame($bar, $barManager->getBar());
        $bar->shouldHaveReceived('addPanel')->with(m::type(IBarPanel::class), 'auth');
        $this->assertNull($barManager->get('terminal'));
    }
}
