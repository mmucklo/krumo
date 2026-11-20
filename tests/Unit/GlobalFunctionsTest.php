<?php

declare(strict_types=1);

namespace Krumo\Tests;

final class GlobalFunctionsTest extends BaseTestCase
{
    public function testKrumoGlobalFunctionDumps(): void
    {
        ob_start();
        krumo('krumo_global_value');
        $output = ob_get_clean();

        $this->assertStringContainsString('krumo_global_value', $output);
        $this->assertStringContainsString('Called from', $output);
    }

    public function testKGlobalFunctionDumps(): void
    {
        ob_start();
        k('k_function_value');
        $output = ob_get_clean();

        $this->assertStringContainsString('k_function_value', $output);
        $this->assertStringContainsString('Called from', $output);
    }

    public function testKWithNineSetsTheDogMarker(): void
    {
        ob_start();
        k(9);
        ob_get_clean();

        $this->assertTrue($GLOBALS['__KRUMO_DOG'] ?? false);

        unset($GLOBALS['__KRUMO_DOG']);
    }

    public function testKdDumpsAndExitsInCli(): void
    {
        [$output, $exitCode] = $this->runPhpScript(
            'kd("kd_cli_value"); echo "NEVER_REACHED";'
        );

        $this->assertSame(0, $exitCode);
        $this->assertStringContainsString('kd_cli_value', $output);
        $this->assertStringContainsString('Called from', $output);
        $this->assertStringNotContainsString('NEVER_REACHED', $output);
    }

    public function testKrumoFromShutdownFunctionDoesNotWarn(): void
    {
        // At shutdown the outermost backtrace frame carries no file/line;
        // cli_dump() used to warn with "Undefined array key" for both.
        [$output, $exitCode] = $this->runPhpScript(
            'register_shutdown_function("krumo", "shutdown_value");'
        );

        $this->assertSame(0, $exitCode);
        $this->assertStringContainsString('shutdown_value', $output);
        $this->assertStringContainsString('Called from', $output);
        $this->assertStringNotContainsString('Warning', $output);
    }

    public function testKdPrintsHtmlHeadersAndExitsUnderCgi(): void
    {
        if (self::phpCgiBinary() === null) {
            $this->markTestSkipped('php-cgi is not available');
        }

        [$output, $exitCode] = $this->runPhpScript(
            'kd("kd_cgi_value"); echo "NEVER_REACHED";',
            self::phpCgiBinary(),
            '-d cgi.force_redirect=0'
        );

        $this->assertSame(0, $exitCode);
        $this->assertStringContainsString('kd_cgi_value', $output);
        $this->assertStringContainsString('<!DOCTYPE html><html><head><meta charset="UTF-8"></head><body>', $output);
        $this->assertStringNotContainsString('NEVER_REACHED', $output);
    }
}
