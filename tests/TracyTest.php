<?php

namespace EvoNext\Tracy\Tests;

use EvoNext\Tracy\Tracy;
use PHPUnit\Framework\TestCase;

class TracyTest extends TestCase
{
    /**
     * @runInSeparateProcess
     */
    public function testInstance()
    {
        $tracy = Tracy::instance([
            'email' => 'recca0120@gmail.com',
            'emailSnooze' => '3 days',
            'enabled' => true,
        ]);
        $databasePanel = $tracy->getPanel('database');
        $databasePanel->logQuery('select * from users');
        $databasePanel->logQuery('select * from news');
        $databasePanel->logQuery('select * from products');

        $this->assertTrue(is_string($databasePanel->getPanel()));

        $authPanel = $tracy->getPanel('auth');
        $authPanel->setUserResolver(function () {
            return ['username' => 'foo'];
        });

        $this->assertTrue(is_string($authPanel->getPanel()));
        $this->assertTrue($tracy->isEnabled());
    }
}
