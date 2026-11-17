<?php

declare(strict_types=1);

namespace Krumo\Tests;

/**
 * Exercises Krumo's HTML output path via php-cgi subprocesses: dump() always
 * short-circuits to var_export() when the process itself is CLI, so the HTML
 * branch (footnotes, skin CSS, sort/expand modes, recursion cleanup) is only
 * reachable from a non-CLI SAPI.
 */
final class HtmlDumpTest extends BaseTestCase
{
    protected function setUp(): void
    {
        if (self::phpCgiBinary() === null) {
            $this->markTestSkipped('php-cgi is not available');
        }

        parent::setUp();
    }

    /**
     * @return array{0: string, 1: int}
     */
    private function runCgiScript(string $body): array
    {
        return $this->runPhpScript($body, self::phpCgiBinary(), '-d cgi.force_redirect=0');
    }

    public function testKrumoReturnCapturesFullHtml(): void
    {
        [$output, $exitCode] = $this->runCgiScript(
            'echo "CAPTURED:" . Krumo::dump("html_return_value", KRUMO_RETURN);'
        );

        $this->assertSame(0, $exitCode);
        $this->assertStringContainsString('CAPTURED:<!DOCTYPE html>', $output);
        $this->assertStringContainsString('krumo-root', $output);
        $this->assertStringContainsString('html_return_value', $output);
        $this->assertStringContainsString('Called from', $output);
        $this->assertStringContainsString('Krumo version', $output);
    }

    public function testExpandAllRendersNodesOpened(): void
    {
        [$collapsed, $exitCode] = $this->runCgiScript(
            'Krumo::dump(["expand_key" => "expand_value"]);'
        );
        [$expanded, $exitCode2] = $this->runCgiScript(
            'Krumo::dump(["expand_key" => "expand_value"], KRUMO_EXPAND_ALL);'
        );

        $this->assertSame(0, $exitCode);
        $this->assertSame(0, $exitCode2);

        // "krumo-expand krumo-opened" (space-joined) only occurs in rendered
        // markup, never in the injected skin CSS or JavaScript
        $this->assertStringNotContainsString('krumo-expand krumo-opened', $collapsed);
        $this->assertStringContainsString('krumo-expand krumo-opened', $expanded);
    }

    public function testSortModesControlKeyOrder(): void
    {
        $data = '["zzz_sort_key" => "one", "aaa_sort_key" => "two"]';

        [$sorted, $exitCode1] = $this->runCgiScript("Krumo::dump($data, KRUMO_SORT);");
        [$unsorted, $exitCode2] = $this->runCgiScript("Krumo::dump($data, KRUMO_NO_SORT);");

        $this->assertSame(0, $exitCode1);
        $this->assertSame(0, $exitCode2);
        $this->assertLessThan(
            strpos($sorted, 'zzz_sort_key'),
            strpos($sorted, 'aaa_sort_key'),
            'KRUMO_SORT should render keys in sorted order'
        );
        $this->assertLessThan(
            strpos($unsorted, 'aaa_sort_key'),
            strpos($unsorted, 'zzz_sort_key'),
            'KRUMO_NO_SORT should render keys in insertion order'
        );
    }

    public function testMultipleArgumentsAreEachDumped(): void
    {
        [$output, $exitCode] = $this->runCgiScript(
            'Krumo::dump("first_html_arg", "second_html_arg", "third_html_arg");'
        );

        $this->assertSame(0, $exitCode);
        $this->assertSame(3, substr_count($output, '<div class="krumo-root">'));
        $this->assertSame(3, substr_count($output, 'Called from'));
        $this->assertStringContainsString('first_html_arg', $output);
        $this->assertStringContainsString('second_html_arg', $output);
        $this->assertStringContainsString('third_html_arg', $output);
    }

    public function testSameObjectAcrossSeparateDumpsIsNotRecursive(): void
    {
        [$output, $exitCode] = $this->runCgiScript(
            '$obj = new stdClass(); $obj->marker = "repeat_marker";'
            . ' Krumo::dump($obj); Krumo::dump($obj);'
        );

        $this->assertSame(0, $exitCode);
        $this->assertStringNotContainsString('Recursion', $output);
        $this->assertSame(2, substr_count($output, 'stdClass'));
        $this->assertSame(2, substr_count($output, 'repeat_marker'));
    }

    public function testSameObjectRepeatedWithinOneCallIsNotRecursive(): void
    {
        [$output, $exitCode] = $this->runCgiScript(
            '$obj = new stdClass(); $obj->marker = "twice_marker";'
            . ' Krumo::dump($obj, $obj);'
        );

        $this->assertSame(0, $exitCode);
        $this->assertStringNotContainsString('Recursion', $output);
        $this->assertSame(2, substr_count($output, 'twice_marker'));
    }

    public function testFetchReturnsHtmlDumpAsString(): void
    {
        [$output, $exitCode] = $this->runCgiScript(
            'echo "FETCHED:" . Krumo::fetch("fetched_value");'
        );

        $this->assertSame(0, $exitCode);
        $this->assertStringContainsString('FETCHED:<!DOCTYPE html>', $output);
        $this->assertStringContainsString('fetched_value', $output);
        $this->assertStringContainsString('krumo-root', $output);
    }

    public function testHeadingRendersHtmlTitleMarkup(): void
    {
        [$output, $exitCode] = $this->runCgiScript('Krumo::request();');

        $this->assertSame(0, $exitCode);
        $this->assertLessThan(
            strpos($output, '<!DOCTYPE html>'),
            strpos($output, '<div class="krumo-title">')
        );
        $this->assertStringContainsString('<div class="krumo-title">', $output);
        $this->assertStringContainsString('<code><b>$_REQUEST</b></code>', $output);
        $this->assertStringContainsString('Array', $output);
    }

    public function testHeadersDumpsRequestHeaders(): void
    {
        [$output, $exitCode] = $this->runCgiScript('Krumo::headers();');

        $this->assertSame(0, $exitCode);
        $this->assertStringContainsString('This is a list of all HTTP request headers.', $output);
        $this->assertStringContainsString('Array', $output);
    }

    public function testHtmlHeadersPrintsPageHeader(): void
    {
        [$output, $exitCode] = $this->runCgiScript('Krumo::htmlHeaders();');

        $this->assertSame(0, $exitCode);
        $this->assertStringContainsString('<!DOCTYPE html><html><head><meta charset="UTF-8"></head><body>', $output);
    }
}
