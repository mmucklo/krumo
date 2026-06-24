<?php

declare(strict_types=1);

namespace Krumo\Tests;

use PHPUnit\Framework\TestCase;
use ReflectionMethod;

abstract class BaseTestCase extends TestCase
{
    protected function setUp(): void
    {
        \Krumo::enable();
        \Krumo::setConfig([]);
        \Krumo::$expand_all = 0;
        \Krumo::$sort = null;
        \Krumo::cascade(null);
    }

    /**
     * Render HTML via the internal _dump() method, bypassing CLI mode check.
     */
    protected function renderHtml(mixed $value): string
    {
        $ref = new ReflectionMethod(\Krumo::class, '_dump');

        ob_start();
        $ref->invokeArgs(null, [&$value]);
        return ob_get_clean();
    }

    /**
     * Get CLI output from dump().
     */
    protected function getCliOutput(mixed ...$args): string
    {
        ob_start();
        foreach ($args as $data) {
            \Krumo::dump($data);
        }
        return ob_get_clean();
    }
}
