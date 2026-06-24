<?php

declare(strict_types=1);

namespace Krumo\Tests;

final class DumpTest extends BaseTestCase
{
    public function testFetchReturnsStringInCliMode(): void
    {
        $output = $this->getCliOutput('hello');

        $this->assertIsString($output);
        $this->assertNotEmpty($output);
    }

    public function testDumpWithNull(): void
    {
        $html = $this->renderHtml(null);

        $this->assertStringContainsString('krumo-null', $html);
        $this->assertStringContainsString('NULL', $html);
    }

    public function testDumpWithBooleanTrue(): void
    {
        $html = $this->renderHtml(true);

        $this->assertStringContainsString('krumo-boolean', $html);
        $this->assertStringContainsString('TRUE', $html);
    }

    public function testDumpWithBooleanFalse(): void
    {
        $html = $this->renderHtml(false);

        $this->assertStringContainsString('krumo-boolean', $html);
        $this->assertStringContainsString('FALSE', $html);
    }

    public function testDumpWithInteger(): void
    {
        $html = $this->renderHtml(42);

        $this->assertStringContainsString('krumo-integer', $html);
        $this->assertStringContainsString('42', $html);
    }

    public function testDumpWithFloat(): void
    {
        $html = $this->renderHtml(3.14);

        $this->assertStringContainsString('krumo-float', $html);
        $this->assertStringContainsString('3.14', $html);
    }

    public function testDumpWithString(): void
    {
        $html = $this->renderHtml('hello world');

        $this->assertStringContainsString('krumo-string', $html);
        $this->assertStringContainsString('hello world', $html);
    }

    public function testDumpWithArray(): void
    {
        $html = $this->renderHtml(['a', 'b', 'c']);

        $this->assertStringContainsString('krumo-type', $html);
        $this->assertStringContainsString('Array', $html);
        $this->assertStringContainsString('krumo-array-length', $html);
        $this->assertStringContainsString('3', $html);
    }

    public function testDumpWithEmptyArray(): void
    {
        $html = $this->renderHtml([]);

        $this->assertStringContainsString('Array', $html);
        $this->assertStringContainsString('0', $html);
    }

    public function testDumpWithAssociativeArray(): void
    {
        $html = $this->renderHtml(['key' => 'value']);

        $this->assertStringContainsString('Array', $html);
        $this->assertStringContainsString('key', $html);
        $this->assertStringContainsString('value', $html);
    }

    public function testDumpWithObject(): void
    {
        $obj = new \stdClass();
        $obj->name = 'test';
        $html = $this->renderHtml($obj);

        $this->assertStringContainsString('Object', $html);
        $this->assertStringContainsString('stdClass', $html);
        $this->assertStringContainsString('name', $html);
        $this->assertStringContainsString('test', $html);
    }

    public function testDumpWithResource(): void
    {
        $fp = fopen('php://memory', 'r');
        $html = $this->renderHtml($fp);
        fclose($fp);

        $this->assertStringContainsString('Resource', $html);
    }

    public function testDumpWithNestedArray(): void
    {
        $data = ['level1' => ['level2' => ['level3' => 'deep']]];
        $html = $this->renderHtml($data);

        $this->assertStringContainsString('level1', $html);
        $this->assertStringContainsString('level2', $html);
        $this->assertStringContainsString('level3', $html);
        $this->assertStringContainsString('deep', $html);
    }

    public function testVersionMethod(): void
    {
        $version = \Krumo::version();

        $this->assertIsString($version);
        $this->assertNotEmpty($version);
    }

    public function testCliOutputContainsValue(): void
    {
        $output = $this->getCliOutput(42);

        $this->assertStringContainsString('42', $output);
    }

    public function testCliOutputContainsString(): void
    {
        $output = $this->getCliOutput('hello');

        $this->assertStringContainsString('hello', $output);
    }
}
