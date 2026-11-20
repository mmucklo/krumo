<?php

declare(strict_types=1);

namespace Krumo\Tests;

final class RecursionTest extends BaseTestCase
{
    public function testNoRecursionInFlatArray(): void
    {
        $html = $this->renderHtml([1, 2, 3]);

        $this->assertStringNotContainsString('Recursion', $html);
    }

    public function testNoRecursionInFlatObject(): void
    {
        $obj = new \stdClass();
        $obj->name = 'test';

        $html = $this->renderHtml($obj);

        $this->assertStringNotContainsString('Recursion', $html);
    }

    public function testCircularObjectReferenceDetectedInHtml(): void
    {
        $obj = new \stdClass();
        $obj->self = $obj;

        $html = $this->renderHtml($obj);

        $this->assertStringContainsString('Recursion', $html);
    }

    public function testCircularArrayReferenceDetectedInHtml(): void
    {
        $a = [];
        $a['self'] =& $a;

        $html = $this->renderHtml($a);

        $this->assertStringContainsString('Recursion', $html);
    }

    public function testSortedCircularArrayReferenceDetectedInHtml(): void
    {
        // The sorted-render path must not break the reference chain that
        // carries the recursion marker.
        $a = ['x' => 1];
        $a['self'] =& $a;

        $html = $this->renderHtml($a);

        $this->assertStringContainsString('Recursion', $html);
        $this->assertStringContainsString('Sorted', $html);
    }

    public function testNestedArraysAreNotFlaggedAsRecursive(): void
    {
        $html = $this->renderHtml(['a' => ['b' => ['c' => 1]]]);

        $this->assertStringNotContainsString('Recursion', $html);
    }

    public function testCircularArrayReferenceViaCli(): void
    {
        $a = [];
        $a[] = &$a;

        $output = $this->getCliOutput($a);

        // var_export() cannot handle circular references and renders them as NULL
        $this->assertStringContainsString('NULL', $output);
    }

    public function testCircularObjectReferenceViaCli(): void
    {
        $obj = new \stdClass();
        $obj->self = $obj;

        $output = $this->getCliOutput($obj);

        // var_export() cannot handle circular references and renders them as NULL
        $this->assertStringContainsString('NULL', $output);
    }

    public function testCircularArrayRendersAndLeavesNoMarkerUnderCgi(): void
    {
        if (self::phpCgiBinary() === null) {
            $this->markTestSkipped('php-cgi is not available');
        }

        // A full HTML dump of a circular array used to recurse forever and
        // exhaust memory; it must render a Recursion marker instead, and the
        // temporary marker key must be removed from the user's array again.
        [$output, $exitCode] = $this->runPhpScript(
            '$a = ["x" => 1]; $a["self"] =& $a; krumo($a);'
            . ' echo "\nREMAINING:", json_encode(array_keys($a));',
            self::phpCgiBinary(),
            '-d memory_limit=64M -d cgi.force_redirect=0'
        );

        $this->assertSame(0, $exitCode);
        $this->assertStringContainsString('Recursion', $output);
        $this->assertStringNotContainsString('Fatal error', $output);
        $this->assertStringContainsString('REMAINING:["x","self"]', $output);
    }

    public function testDumpDoesNotMutateTheUserArrayUnderCgi(): void
    {
        if (self::phpCgiBinary() === null) {
            $this->markTestSkipped('php-cgi is not available');
        }

        // Sorting for display must not reorder the user's array, and the
        // recursion marker must not leak into it.
        [$output, $exitCode] = $this->runPhpScript(
            '$a = ["z" => 1, "a" => 2, "m" => 3]; krumo($a);'
            . ' echo "\nREMAINING:", json_encode(array_keys($a));',
            self::phpCgiBinary(),
            '-d cgi.force_redirect=0'
        );

        $this->assertSame(0, $exitCode);
        $this->assertStringContainsString('Sorted', $output);
        $this->assertStringContainsString('REMAINING:["z","a","m"]', $output);
    }
}
