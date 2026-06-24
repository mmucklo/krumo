<?php

declare(strict_types=1);

namespace Krumo\Tests;

final class StringTest extends BaseTestCase
{
    public function testShortStringNotTruncated(): void
    {
        $html = $this->renderHtml('hello');

        $this->assertStringContainsString('hello', $html);
        $this->assertStringNotContainsString('krumo-expand', $html);
    }

    public function testLongStringIsTruncated(): void
    {
        $longString = str_repeat('a', 200);
        $html = $this->renderHtml($longString);

        $this->assertStringContainsString('krumo-expand', $html);
    }

    public function testStringTruncationWithCustomLength(): void
    {
        \Krumo::setConfig(['display' => ['truncate_string_length' => 5]]);

        $html = $this->renderHtml('hello world');

        $this->assertStringContainsString('krumo-expand', $html);
    }

    public function testStringWithNewlines(): void
    {
        $html = $this->renderHtml("line1\nline2");

        $this->assertStringContainsString('krumo-carrage-return', $html);
    }

    public function testStringWithCarriageReturns(): void
    {
        $html = $this->renderHtml("line1\rline2");

        $this->assertStringContainsString('krumo-carrage-return', $html);
    }

    public function testStringWithCrLf(): void
    {
        $html = $this->renderHtml("line1\r\nline2");

        $this->assertStringContainsString('krumo-carrage-return', $html);
    }

    public function testStringWithLeadingWhitespace(): void
    {
        $html = $this->renderHtml('  hello');

        $this->assertStringContainsString('&#9251;', $html);
    }

    public function testStringWithTrailingWhitespace(): void
    {
        $html = $this->renderHtml('hello  ');

        $this->assertStringContainsString('&#9251;', $html);
    }

    public function testEmptyString(): void
    {
        $html = $this->renderHtml('');

        $this->assertStringContainsString('krumo-string', $html);
        $this->assertStringContainsString('String(', $html);
    }

    public function testStringLengthDisplayed(): void
    {
        $html = $this->renderHtml('abc');

        $this->assertStringContainsString('krumo-string-length', $html);
        $this->assertStringContainsString('3', $html);
    }
}
