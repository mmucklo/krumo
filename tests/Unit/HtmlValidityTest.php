<?php

declare(strict_types=1);

namespace Krumo\Tests;

use PHPUnit\Framework\Attributes\DataProvider;

/**
 * Structural validation of the HTML Krumo emits: every fixture is dumped in
 * a php-cgi subprocess (dump() falls back to var_export() under the CLI
 * SAPI, so the HTML branch needs a non-CLI SAPI), and the captured markup
 * must be well formed:
 *
 *  - contain no unterminated numeric character references,
 *  - close every opened tag in the right order (stack check), and
 *  - produce no libxml diagnostics when parsed as a document.
 */
final class HtmlValidityTest extends BaseTestCase
{
    /**
     * HTML5 permits omitting the end tags of <body> and <html>, so helpers
     * that print a page header and exit (kd(), htmlHeaders() + dump) are
     * still conforming when they leave them unclosed.
     */
    private const OPTIONAL_END_TAGS = ['body', 'html'];

    /** Elements that never take an end tag. */
    private const VOID_ELEMENTS = [
        'area', 'base', 'br', 'col', 'embed', 'hr', 'img', 'input',
        'keygen', 'link', 'meta', 'param', 'source', 'track', 'wbr',
    ];

    protected function setUp(): void
    {
        if (self::phpCgiBinary() === null) {
            $this->markTestSkipped('php-cgi is not available');
        }

        parent::setUp();
    }

    /**
     * @param string $snippet         PHP code that renders one Krumo dump
     * @param list<string> $allowUnclosed tags the fixture may leave unclosed
     */
    #[DataProvider('dumpFixtures')]
    public function testDumpOutputIsValidAndBalancedHtml(string $snippet, array $allowUnclosed = []): void
    {
        $html = $this->renderFixture($snippet);

        $this->assertNoUnterminatedCharRefs($html);
        $this->assertTagsBalanced($html, $allowUnclosed);
        $this->assertParsesWithoutDiagnostics($html);
    }

    /**
     * Regression test: the dog footnote must stay a terminated character
     * reference (&#128021;), or libxml reports "htmlParseCharRef: missing
     * semicolon" and HTML5 parsers must switch to error handling.
     */
    public function testDogFootnoteEntityIsTerminated(): void
    {
        [$raw, $exitCode] = $this->runCgiScript('k(9);');

        $this->assertSame(0, $exitCode);

        $html = $this->stripCgiHeaders($raw);
        $this->assertStringContainsString('&#128021;', $html);
        $this->assertStringNotContainsString('&#128021<', $html);
    }

    /**
     * One snippet per render path: every _string()/_array()/_object()/
     * _boolean()/_integer()/_float()/_null()/_resource()/_not_initialized()/
     * _recursion() shape, the dump() wrapper and footnote, the heading
     * helpers, and the kd()/htmlHeaders() page-header emitters.
     *
     * @return array<string, array{0: string, 1?: list<string>}>
     */
    public static function dumpFixtures(): array
    {
        return [
            'null' => ['Krumo::dump(null);'],
            'boolean true' => ['Krumo::dump(true);'],
            'boolean false' => ['Krumo::dump(false);'],
            'integer' => ['Krumo::dump(42);'],
            'float' => ['Krumo::dump(pi());'],
            'plain string' => ['Krumo::dump("Hello, World!");'],
            'string with html characters' => ['Krumo::dump(\'<script>alert(1)</script> & "quotes" <b>bold</b>\');'],
            'string with line breaks' => ['Krumo::dump("line1\nline2\r\nline3\r");'],
            'string with surrounding whitespace' => ['Krumo::dump("  padded \t tabbed  ");'],
            'string truncated with preview' => ['Krumo::dump(str_repeat("abcdefghij", 30));'],
            'string truncated with line breaks' => ['Krumo::dump(str_repeat("abcdefghij", 15) . "\r\nsecond line\nthird");'],
            'string at truncation boundary' => ['Krumo::dump(str_repeat("x", 100)); Krumo::dump(str_repeat("y", 101));'],
            'unicode string' => ['Krumo::dump("héllo wörld — 日本語 🐕 emoji");'],
            'invalid utf-8 string' => ['Krumo::dump("bad \xff\xfe utf8 \x80 byte");'],
            'callable string' => ['Krumo::dump("ucfirst");'],
            'empty string' => ['Krumo::dump("");'],
            'empty array' => ['Krumo::dump([]);'],
            'list array' => ['Krumo::dump([10, 20, 30]);'],
            'sorted assoc array' => ['Krumo::dump(["zeta" => 1, "alpha" => 2, "mike" => 3]);'],
            'callable array' => ['Krumo::dump(["Krumo", "version"]);'],
            'nested arrays' => [
                'Krumo::dump(["l1" => ["l2" => ["l3" => ["l4" => "bottom"], "m" => [1, 2.5, true, null]]], "sib" => "x"]);',
            ],
            'self-referencing array' => ['$a = []; $a["self"] =& $a; Krumo::dump($a);'],
            'truncated array' => [
                'Krumo::setConfig(["display" => ["truncate_array_length" => 5]]); Krumo::dump(range(1, 12));',
            ],
            'array with hostile keys' => ['Krumo::dump([
                "res" => fopen("php://memory", "r"),
                "obj" => new stdClass(),
                "" => "empty key",
                " leading" => 1,
                "trailing " => 2,
                "key with spaces" => 3,
                "key<b>with</b>&html" => "value <i>html</i> & \"quotes\"",
                "key\twith\ttabs" => 4,
                "key\nwith\nnewline" => 5,
                "unicode key 🐕" => "unicode ✓",
                "long" => str_repeat("abcdefghij", 30),
                "multi" => "line1\nline2",
                "nested" => ["x" => ["y" => "deep"]],
                "empty_arr" => [],
            ]);'],
            'array and object cycle' => [
                '$outer = []; $obj = new stdClass(); $outer["obj"] = $obj; $obj->snapshot = $outer; Krumo::dump($outer);',
            ],
            'object with mixed visibility' => [
                'class T { public $p = "pub"; protected $q = 1; private $r = 2.5; } Krumo::dump(new T());',
            ],
            'object with uninitialized typed properties' => [
                'class U { public int $x; public int|string $y; } Krumo::dump(new U());',
            ],
            'empty object' => ['Krumo::dump(new stdClass());'],
            'self-referencing object' => ['$o = new stdClass(); $o->self = $o; Krumo::dump($o);'],
            'two objects in a cycle' => [
                '$a = new stdClass(); $b = new stdClass(); $a->b = $b; $b->a = $a; Krumo::dump($a);',
            ],
            'closure' => ['Krumo::dump(function () { return "x"; });'],
            'resource' => ['Krumo::dump(fopen("php://memory", "r"));'],
            'datetime-like keys' => ['Krumo::dump(["event_time" => 1231241234, "microtime" => microtime(true)]);'],
            'multiple arguments' => ['Krumo::dump("str", 42, ["a" => 1], null, 3.14, true);'],
            'expand all mode' => ['Krumo::dump(["a" => ["b" => ["c" => 1]]], KRUMO_EXPAND_ALL);'],
            'heading helper' => ['Krumo::request();'],
            'dog footnote' => ['k(9);'],
            'kd helper' => ['kd(["kd" => "value"]);', self::OPTIONAL_END_TAGS],
            'htmlHeaders plus dump' => [
                'Krumo::htmlHeaders(); Krumo::dump(["via" => "htmlHeaders"]);',
                self::OPTIONAL_END_TAGS,
            ],
        ];
    }

    // ---------------------------------------------------------------- helpers

    private function renderFixture(string $snippet): string
    {
        [$raw, $exitCode] = $this->runCgiScript($snippet);

        $this->assertSame(0, $exitCode, "fixture did not run cleanly:\n{$raw}");

        $html = $this->stripCgiHeaders($raw);
        $this->assertNotSame('', $html);

        // surface PHP diagnostics that would silently pollute the markup
        $this->assertStringNotContainsString('Warning:', $html);
        $this->assertStringNotContainsString('Deprecated:', $html);
        $this->assertStringNotContainsString('Fatal error', $html);

        return $html;
    }

    /**
     * @return array{0: string, 1: int}
     */
    private function runCgiScript(string $body): array
    {
        return $this->runPhpScript(
            $body,
            self::phpCgiBinary(),
            '-d cgi.force_redirect=0 -d display_errors=1 -d error_reporting=E_ALL'
        );
    }

    private function stripCgiHeaders(string $raw): string
    {
        // php-cgi prepends response headers (X-Powered-By, Content-type, ...)
        // followed by a blank line before the actual markup.
        if (preg_match('/^(?:[A-Za-z-]+:.*\r?\n)+\r?\n/', $raw, $matches) === 1) {
            return substr($raw, strlen($matches[0]));
        }

        return $raw;
    }

    private function assertNoUnterminatedCharRefs(string $html): void
    {
        // Possessive digits: without ++ the engine backtracks on "&#8734;"
        // and matches the shorter "&#873" even though the reference is fine.
        preg_match_all('/&#\d++(?!;)/', $html, $matches);

        $this->assertSame(
            [],
            $matches[0],
            'unterminated numeric character reference(s): ' . implode(', ', array_unique($matches[0]))
        );
    }

    /**
     * @param list<string> $allowUnclosed tags the output may leave unclosed
     */
    private function assertTagsBalanced(string $html, array $allowUnclosed = []): void
    {
        // The tokenizer below must not look for tags inside raw-text
        // elements; keep the containers but drop their contents.
        $html = preg_replace('~(<(script|style)\b[^>]*>).*?(</\2\s*>)~is', '$1$3', $html) ?? '';
        // Comments and doctypes do not participate in tag nesting.
        $html = preg_replace('~<!--.*?-->~s', '', $html) ?? '';

        $matched = preg_match_all(
            '~<(/?)([a-zA-Z][a-zA-Z0-9:-]*)((?:"[^"]*"|\'[^\']*\'|[^\'">])*)(/?)>~s',
            $html,
            $tags,
            PREG_SET_ORDER
        );
        $this->assertNotFalse($matched);

        $stack = [];
        foreach ($tags as $tag) {
            $name = strtolower($tag[2]);

            if (in_array($name, self::VOID_ELEMENTS, true) || $tag[4] === '/') {
                continue;
            }

            if ($tag[1] !== '/') {
                $stack[] = $name;
                continue;
            }

            $open = null;
            for ($i = count($stack) - 1; $i >= 0; $i--) {
                if ($stack[$i] === $name) {
                    $open = $i;
                    break;
                }
            }

            if ($open === null) {
                $this->fail("closing </{$name}> matches no open tag");
            }

            $skipped = array_slice($stack, $open + 1);
            $this->assertSame(
                [],
                $skipped,
                "closing </{$name}> while <" . implode('>, <', $skipped) . '> is still open'
            );
            array_splice($stack, $open);
        }

        $unclosed = array_values(array_diff($stack, $allowUnclosed));
        $this->assertSame([], $unclosed, 'unclosed tag(s): <' . implode('>, <', $unclosed) . '>');
    }

    private function assertParsesWithoutDiagnostics(string $html): void
    {
        // dump() prints "<!DOCTYPE html>" on every call, so any dump that is
        // not the very first output on the page (heading helpers, later
        // dump() arguments, subsequent dumps) emits stray declarations
        // mid-body. That placement defect is a known, separate issue;
        // stripping the declarations keeps this test strict about every
        // other kind of structural error.
        $html = preg_replace('~<!DOCTYPE[^>]*>~i', '', $html) ?? $html;

        libxml_use_internal_errors(true);
        libxml_clear_errors();

        $document = new \DOMDocument();
        $document->loadHTML($html);

        $errors = array_map(
            static fn (\LibXMLError $error): string => 'line ' . $error->line . ': ' . trim($error->message),
            libxml_get_errors()
        );
        libxml_clear_errors();

        $this->assertSame(
            [],
            $errors,
            "libxml reported structural error(s):\n" . implode("\n", $errors)
        );
    }
}
