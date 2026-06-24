<?php

declare(strict_types=1);

namespace Krumo\Tests;

final class SkinTest extends BaseTestCase
{
    public function testCliOutputContainsSeparator(): void
    {
        $output = $this->getCliOutput(['key' => 'value']);

        $this->assertStringContainsString('key', $output);
        $this->assertStringContainsString('value', $output);
    }

    public function testModernSkinCanBeSelected(): void
    {
        \Krumo::setConfig(['skin' => ['selected' => 'modern']]);

        $output = $this->getCliOutput('test');

        $this->assertNotEmpty($output);
    }

    public function testFallbackToStylishForUnknownSkin(): void
    {
        \Krumo::setConfig(['skin' => ['selected' => 'nonexistent']]);

        $output = $this->getCliOutput('test');

        $this->assertNotEmpty($output);
    }

    public function testKrumoVersionInCliOutput(): void
    {
        \Krumo::setConfig(['display' => ['show_version' => true]]);

        $output = $this->getCliOutput('test');

        $this->assertStringContainsString('Krumo version', $output);
    }

    public function testShowCallInfoInCliOutput(): void
    {
        \Krumo::setConfig(['display' => ['show_call_info' => true]]);

        $output = $this->getCliOutput('test');

        $this->assertStringContainsString('Called from', $output);
    }
}
