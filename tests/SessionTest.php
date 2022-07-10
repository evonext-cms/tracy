<?php

namespace EvoNext\Tracy\Tests;

use EvoNext\Tracy\Session\Session;
use PHPUnit\Framework\TestCase;

class SessionTest extends TestCase
{
    /**
     * @runInSeparateProcess
     */
    public function testStart()
    {
        $session = new Session();

        $this->assertFalse($session->isStarted());
        $session->start();
        $this->assertTrue($session->isStarted());
    }
}
