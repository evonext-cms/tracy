<?php

namespace EvoNext\Tracy\Tests;

use EvoNext\Tracy\Template;
use PHPUnit\Framework\TestCase;

class TemplateTest extends TestCase
{
    public function testRenderMinify()
    {
        $template = new Template();
        $template->setAttributes([
            'id' => 'foo',
            'rows' => [
                'foo' => 'bar',
            ],
        ])->minify(true);

        $expected = implode(' ', [
            '<span title="Logged in">',
            '<svg viewBox="0 -50 2048 2048">',
            '<path fill="#61A519" d="m1615 1803.5c-122 17-246 7-369 8-255 1-510 3-765-1-136-2-266-111-273-250-11-192 11-290.5 115-457.5 62-100 192-191 303-147 110 44 201 130 321 149 160 25 317-39 446-130 82-58 200-9 268 51 157 173 186.8 275.49 184 484.49-1.9692 147.11-108.91 271.41-230 293zm-144-1226.5c0 239-208 447-447 447s-447-208-447-447 208-447 447-447c240 1 446 207 447 447z"/>',
            '</svg>',
            '<span class="tracy-label">',
            'foo',
            '</span>',
        ]).'</span>';

        $this->assertSame(str_replace("\r\n", "\n", $expected), str_replace("\r\n", "\n", $template->render(__DIR__.'/../resources/views/AuthPanel/tab.php')));
    }

    public function testRender()
    {
        $template = new Template();
        $template->setAttributes([
            'id' => 'foo',
            'rows' => [
                'foo' => 'bar',
            ],
        ])->minify(false);

        $expected = '<span title="Logged in">
    <svg viewBox="0 -50 2048 2048">
        <path fill="#61A519" d="m1615 1803.5c-122 17-246 7-369 8-255 1-510 3-765-1-136-2-266-111-273-250-11-192 11-290.5 115-457.5 62-100 192-191 303-147 110 44 201 130 321 149 160 25 317-39 446-130 82-58 200-9 268 51 157 173 186.8 275.49 184 484.49-1.9692 147.11-108.91 271.41-230 293zm-144-1226.5c0 239-208 447-447 447s-447-208-447-447 208-447 447-447c240 1 446 207 447 447z"/>
    </svg>
    <span class="tracy-label">
        foo    </span>
</span>
';

        $this->assertSame(str_replace("\r\n", "\n", $expected), str_replace("\r\n", "\n", $template->render(__DIR__.'/../resources/views/AuthPanel/tab.php')));
    }
}
