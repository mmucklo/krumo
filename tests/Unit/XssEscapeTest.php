<?php

declare(strict_types=1);

namespace Krumo\Tests;

final class XssEscapeTest extends BaseTestCase
{
    public function testStringWithHtmlTagsIsEscaped(): void
    {
        $html = $this->renderHtml('<script>alert("xss")</script>');

        $this->assertStringNotContainsString('<script>', $html);
        $this->assertStringContainsString('&lt;script&gt;', $html);
    }

    public function testStringWithAmpersandIsEscaped(): void
    {
        $html = $this->renderHtml('a & b');

        $this->assertStringContainsString('a &amp; b', $html);
    }

    public function testStringWithQuotesIsEscaped(): void
    {
        $html = $this->renderHtml('he said "hello"');

        $this->assertStringContainsString('he said &quot;hello&quot;', $html);
    }

    public function testArrayKeyWithHtmlIsEscaped(): void
    {
        $html = $this->renderHtml(['<b>bold</b>' => 'value']);

        $this->assertStringNotContainsString('<b>bold</b>', $html);
        $this->assertStringContainsString('&lt;b&gt;bold&lt;/b&gt;', $html);
    }

    public function testObjectNameWithSpecialCharsIsEscaped(): void
    {
        $obj = new \stdClass();
        $obj->{'key<script>'} = 'value';
        $html = $this->renderHtml($obj);

        $this->assertStringNotContainsString('<script>', $html);
        $this->assertStringContainsString('&lt;script&gt;', $html);
    }

    public function testClassNameIsPresent(): void
    {
        $obj = new \stdClass();
        $html = $this->renderHtml($obj);

        $this->assertStringContainsString('stdClass', $html);
    }

    public function testNullOutputIsValidHtml(): void
    {
        $html = $this->renderHtml(null);

        $this->assertStringContainsString('krumo-null', $html);
    }
}
