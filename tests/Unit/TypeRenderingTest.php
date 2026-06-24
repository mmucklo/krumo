<?php

declare(strict_types=1);

namespace Krumo\Tests;

final class TypeRenderingTest extends BaseTestCase
{
    public function testNullType(): void
    {
        $html = $this->renderHtml(null);

        $this->assertStringContainsString('krumo-type krumo-null', $html);
    }

    public function testBooleanType(): void
    {
        $html = $this->renderHtml(true);

        $this->assertStringContainsString('krumo-type">Boolean', $html);
        $this->assertStringContainsString('krumo-boolean', $html);
    }

    public function testIntegerType(): void
    {
        $html = $this->renderHtml(42);

        $this->assertStringContainsString('krumo-type">Integer', $html);
        $this->assertStringContainsString('krumo-integer', $html);
    }

    public function testFloatType(): void
    {
        $html = $this->renderHtml(3.14);

        $this->assertStringContainsString('krumo-type">Float', $html);
        $this->assertStringContainsString('krumo-float', $html);
    }

    public function testStringType(): void
    {
        $html = $this->renderHtml('hello');

        $this->assertStringContainsString('krumo-type">String(', $html);
        $this->assertStringContainsString('krumo-string-length', $html);
        $this->assertStringContainsString('krumo-string', $html);
    }

    public function testStringLengthIsDisplayed(): void
    {
        $html = $this->renderHtml('hello');

        $this->assertStringContainsString('5', $html);
    }

    public function testArrayType(): void
    {
        $html = $this->renderHtml(['a', 'b']);

        $this->assertStringContainsString('krumo-type">Array(', $html);
        $this->assertStringContainsString('krumo-array-length', $html);
    }

    public function testObjectType(): void
    {
        $obj = new \stdClass();
        $html = $this->renderHtml($obj);

        $this->assertStringContainsString('krumo-type">Object', $html);
        $this->assertStringContainsString('krumo-class', $html);
        $this->assertStringContainsString('stdClass', $html);
    }

    public function testResourceType(): void
    {
        $fp = fopen('php://memory', 'r');
        $html = $this->renderHtml($fp);
        fclose($fp);

        $this->assertStringContainsString('krumo-type">Resource', $html);
        $this->assertStringContainsString('krumo-resource', $html);
    }

    public function testObjectNameWithPrivateProtectedPublic(): void
    {
        $obj = new TestObjectWithProperties();
        $html = $this->renderHtml($obj);

        $this->assertStringContainsString('public', $html);
        $this->assertStringContainsString('private', $html);
        $this->assertStringContainsString('protected', $html);
    }
}

class TestObjectWithProperties
{
    public string $pub = 'public';
    private string $priv = 'private';
    protected string $prot = 'protected';
}
