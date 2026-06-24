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

    public function testCircularArrayReferenceViaCli(): void
    {
        $a = [];
        $a[] = &$a;

        $output = $this->getCliOutput($a);

        $this->assertNotEmpty($output);
    }

    public function testCircularObjectReferenceViaCli(): void
    {
        $obj = new \stdClass();
        $obj->self = $obj;

        $output = $this->getCliOutput($obj);

        $this->assertNotEmpty($output);
    }
}
