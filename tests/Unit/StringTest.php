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

    public function testInvalidUtf8StringIsSubstitutedNotDropped(): void
    {
        // htmlentities() used to return "" for invalid sequences, silently
        // dropping the dumped string; ENT_SUBSTITUTE shows U+FFFD instead.
        $html = $this->renderHtml("bad \xC3\x28 end");

        $this->assertStringContainsString('bad ', $html);
        $this->assertStringContainsString(' end', $html);
        $this->assertStringContainsString(\chr(0xEF) . \chr(0xBF) . \chr(0xBD), $html);
        $this->assertStringNotContainsString("\xC3\x28", $html);
    }

    public function testBogusConfigCharsetFallsBackToUtf8(): void
    {
        // A bogus display.default_charset used to abort every dump with a
        // ValueError from mb_strlen().
        \Krumo::setConfig(['display' => ['default_charset' => 'not-a-charset']]);

        $html = $this->renderHtml('héllo wörld');

        $this->assertStringContainsString('héllo wörld', $html);
    }

    public function testLongStringPreviewIsEscaped(): void
    {
        $data = "<b>line1</b>\n" . str_repeat('x', 150);
        $html = $this->renderHtml($data);

        $this->assertStringContainsString('krumo-preview', $html);
        $this->assertStringContainsString('&lt;b&gt;line1&lt;/b&gt;', $html);
        $this->assertStringNotContainsString('<b>line1</b>', $html);
    }

    public function testBogusPhpIniDefaultCharsetDoesNotWarnUnderCgi(): void
    {
        if (self::phpCgiBinary() === null) {
            $this->markTestSkipped('php-cgi is not available');
        }

        // The string renderer used to call htmlentities() without a charset,
        // so a bogus php.ini default_charset warned on every dump.
        [$output, $exitCode] = $this->runPhpScript(
            'krumo("charset_probe");',
            self::phpCgiBinary(),
            '-d default_charset=notacharset -d display_errors=1 -d error_reporting=E_ALL -d cgi.force_redirect=0'
        );

        $this->assertSame(0, $exitCode);
        $this->assertStringContainsString('charset_probe', $output);

        // PHP itself may complain at startup about the bogus -d value; the
        // regression target is the warning raised while rendering the dump.
        $this->assertStringNotContainsString('not supported', $output);
        $this->assertStringNotContainsString('htmlentities', $output);
        $this->assertStringNotContainsString('class.krumo.php on line', $output);
    }
}
