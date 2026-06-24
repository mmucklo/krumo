<?php

declare(strict_types=1);

namespace Krumo\Tests;

final class ConfigTest extends BaseTestCase
{
    public function testDefaultSeparator(): void
    {
        $html = $this->renderHtml(['key' => 'value']);

        $this->assertStringContainsString(' =&gt; ', $html);
    }

    public function testCustomSeparator(): void
    {
        \Krumo::setConfig(['display' => ['separator' => ' :: ']]);

        $html = $this->renderHtml(['key' => 'value']);

        $this->assertStringContainsString(' :: ', $html);
    }

    public function testTruncateStringDefault(): void
    {
        $longString = str_repeat('a', 200);
        $html = $this->renderHtml($longString);

        $this->assertStringContainsString('krumo-expand', $html);
    }

    public function testTruncateStringCustomLength(): void
    {
        \Krumo::setConfig(['display' => ['truncate_string_length' => 10]]);

        $longString = str_repeat('a', 50);
        $html = $this->renderHtml($longString);

        $this->assertStringContainsString('krumo-expand', $html);
    }

    public function testTruncateArrayLength(): void
    {
        \Krumo::setConfig(['display' => ['truncate_array_length' => 2]]);

        $data = range(1, 10);
        $html = $this->renderHtml($data);

        $this->assertStringContainsString('truncated', $html);
    }

    public function testCascadeConfigCollapsesNodes(): void
    {
        \Krumo::cascade([0]);

        $data = range(1, 5);
        $html = $this->renderHtml($data);

        $this->assertStringContainsString('display: none;', $html);
    }

    public function testExpandAllOverridesCollapse(): void
    {
        \Krumo::$expand_all = true;

        $data = range(1, 5);
        $html = $this->renderHtml($data);

        $this->assertStringNotContainsString('display: none;', $html);
    }

    public function testShowCarriageReturns(): void
    {
        \Krumo::setConfig(['display' => ['show_carriage_returns' => true]]);

        $html = $this->renderHtml("line1\nline2");

        $this->assertStringContainsString('krumo-carrage-return', $html);
    }

    public function testHideCarriageReturns(): void
    {
        \Krumo::setConfig(['display' => ['show_carriage_returns' => false]]);

        $html = $this->renderHtml("line1\nline2");

        $this->assertStringNotContainsString('krumo-carrage-return', $html);
    }
}
