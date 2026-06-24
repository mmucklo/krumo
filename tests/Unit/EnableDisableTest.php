<?php

declare(strict_types=1);

namespace Krumo\Tests;

final class EnableDisableTest extends BaseTestCase
{
    public function testEnableReturnsTrue(): void
    {
        $result = \Krumo::enable();

        $this->assertTrue($result);
    }

    public function testDisableReturnsTrue(): void
    {
        $result = \Krumo::disable();

        $this->assertTrue($result);
    }

    public function testEnabledDumpProducesOutput(): void
    {
        \Krumo::enable();

        $output = $this->getCliOutput('test');

        $this->assertNotEmpty($output);
    }

    public function testDisableThenEnableRestoresBehavior(): void
    {
        \Krumo::disable();
        \Krumo::enable();

        $output = $this->getCliOutput('test');

        $this->assertNotEmpty($output);
    }

    public function testDisabledFetchReturnsFalse(): void
    {
        \Krumo::disable();

        $result = \Krumo::fetch('test');

        $this->assertFalse($result);
    }

    public function testEnabledFetchReturnsString(): void
    {
        \Krumo::enable();

        $result = \Krumo::fetch('test');

        $this->assertIsString($result);
        $this->assertNotEmpty($result);
    }
}
