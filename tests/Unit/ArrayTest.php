<?php

declare(strict_types=1);

namespace Krumo\Tests;

final class ArrayTest extends BaseTestCase
{
    public function testAssociativeArrayIsSorted(): void
    {
        $data = ['z' => 1, 'a' => 2, 'm' => 3];
        $html = $this->renderHtml($data);

        $aPos = strpos($html, '>a<');
        $mPos = strpos($html, '>m<');
        $zPos = strpos($html, '>z<');

        $this->assertNotFalse($aPos);
        $this->assertNotFalse($mPos);
        $this->assertNotFalse($zPos);
        $this->assertLessThan($mPos, $aPos);
        $this->assertLessThan($zPos, $mPos);
    }

    public function testNoSortPreservesOrder(): void
    {
        \Krumo::$sort = false;

        $data = ['z' => 1, 'a' => 2, 'm' => 3];
        $html = $this->renderHtml($data);

        $zPos = strpos($html, '>z<');
        $aPos = strpos($html, '>a<');

        $this->assertNotFalse($zPos);
        $this->assertNotFalse($aPos);
        $this->assertLessThan($aPos, $zPos);
    }

    public function testIndexedArrayNotSorted(): void
    {
        $data = [3, 1, 2];
        $html = $this->renderHtml($data);

        $this->assertStringContainsString('3', $html);
        $this->assertStringContainsString('1', $html);
        $this->assertStringContainsString('2', $html);
    }

    public function testEmptyArrayShowsZeroCount(): void
    {
        $html = $this->renderHtml([]);

        $this->assertStringContainsString('Array', $html);
        $this->assertStringContainsString('>0<', $html);
    }

    public function testArrayCountDisplayed(): void
    {
        $html = $this->renderHtml(['a', 'b', 'c', 'd', 'e']);

        $this->assertStringContainsString('5', $html);
    }

    public function testExpandAllShowsAllNodes(): void
    {
        \Krumo::$expand_all = true;

        $data = ['a' => [1, 2, 3], 'b' => [4, 5, 6]];
        $html = $this->renderHtml($data);

        $this->assertStringNotContainsString('display: none;', $html);
    }

    public function testSortIndicatorPresent(): void
    {
        \Krumo::$sort = true;

        $data = ['z' => 1, 'a' => 2];
        $html = $this->renderHtml($data);

        $this->assertStringContainsString('Sorted', $html);
    }
}
