<?php

namespace EvoNext\Tracy\Tests\Panels;

use DateTime;
use EvoNext\Tracy\Panels\Helper;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;

class HelperTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    public function testHighlight()
    {
        $pdo = m::mock('PDO');
        $pdo->allows('quote')->andReturnUsing(function ($param) {
            return addslashes($param);
        });

        $now = new DateTime('now');
        $fp = fopen(__FILE__, 'r');
        $this->assertSame(
            'SELECT *, id, name, NOW() AS n FROM users WHERE name LIKE "%foo%" AND id = (1, 2) AND created_at = \''.$now->format('Y-m-d H:i:s').'\' AND resource = &lt;stream resource&gt; ORDER BY RAND(); /** **/ **foo**',
            preg_replace(
                "/\n/",
                '',
                strip_tags(
                    Helper::highlight(
                        'SELECT *, id, name, NOW() AS n FROM users WHERE name LIKE "%?%" AND id = ? AND created_at = ? AND resource = ? ORDER BY RAND(); /** **/ **foo**',
                        ['foo', [1, 2], $now, $fp],
                        $pdo
                    )
                )
            )
        );
        fclose($fp);
    }

    public function testPerformQueryAnalysis()
    {
        $version = 5.0;
        $driver = 'mysql';

        $sql = 'SELECT * FROM users ORDER BY RAND()';
        $this->assertNotNull(Helper::performQueryAnalysis($sql, $version, $driver));

        $sql = 'SELECT * FROM users WHERE id != 1';
        $this->assertNotNull(Helper::performQueryAnalysis($sql, $version, $driver));

        $sql = 'SELECT * FROM users LIMIT 1';
        $this->assertNotNull(Helper::performQueryAnalysis($sql, $version, $driver));

        $sql = 'SELECT * FROM users WHERE name LIKE "foo%"';
        $this->assertNotNull(Helper::performQueryAnalysis($sql, $version, $driver));

        $sql = 'SELECT * FROM users WHERE name LIKE "%foo%"';
        $this->assertNotNull(Helper::performQueryAnalysis($sql, $version, $driver));

        $sql = 'SELECT * FROM users WHERE id IN (SELECT user_id FROM roles)';
        $this->assertNotNull(Helper::performQueryAnalysis($sql, $version, $driver));
    }
}
