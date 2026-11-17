<?php

declare(strict_types=1);

namespace Krumo\Tests;

final class InfoHelpersTest extends BaseTestCase
{
    /**
     * Capture the CLI output produced by one of Krumo's info helpers.
     */
    private function captureHelperOutput(callable $call): string
    {
        ob_start();
        $call();

        return (string) ob_get_clean();
    }

    public function testBacktraceDumpsFrames(): void
    {
        $output = $this->captureHelperOutput(\Krumo::backtrace(...));

        $this->assertStringContainsString("'function'", $output);
        $this->assertStringContainsString('backtrace', $output);
    }

    public function testClasses(): void
    {
        $output = $this->captureHelperOutput(\Krumo::classes(...));

        $this->assertStringContainsString('This is a list of all currently declared classes.', $output);
        $this->assertStringContainsString('stdClass', $output);
    }

    public function testInterfaces(): void
    {
        $output = $this->captureHelperOutput(\Krumo::interfaces(...));

        $this->assertStringContainsString('This is a list of all currently declared interfaces.', $output);
        $this->assertStringContainsString('Stringable', $output);
    }

    public function testIncludes(): void
    {
        $output = $this->captureHelperOutput(\Krumo::includes(...));

        $this->assertStringContainsString('This is a list of all currently included (or required) files.', $output);
        $this->assertStringContainsString('class.krumo.php', $output);
    }

    public function testFunctions(): void
    {
        $output = $this->captureHelperOutput(\Krumo::functions(...));

        $this->assertStringContainsString('This is a list of all currently declared functions.', $output);
        $this->assertStringContainsString('var_dump', $output);
    }

    public function testDefines(): void
    {
        $output = $this->captureHelperOutput(\Krumo::defines(...));

        $this->assertStringContainsString('This is a list of all currently declared constants (defines).', $output);
        $this->assertStringContainsString('PHP_EOL', $output);
    }

    public function testExtensions(): void
    {
        $output = $this->captureHelperOutput(\Krumo::extensions(...));

        $this->assertStringContainsString('This is a list of all currently loaded PHP extensions.', $output);
        $this->assertStringContainsString('Core', $output);
    }

    public function testPhpini(): void
    {
        $iniPath = (string) get_cfg_var('cfg_file_path');
        $output = $this->captureHelperOutput(\Krumo::phpini(...));

        if ($iniPath !== '' && is_readable($iniPath)) {
            $this->assertStringContainsString('This is a list of the configuration settings read from', $output);
            $this->assertStringContainsString('array (', $output);
        } else {
            // Without a readable php.ini the helper bails out without output
            $this->assertSame('', trim($output));
        }
    }

    public function testConf(): void
    {
        $output = $this->captureHelperOutput(\Krumo::conf(...));

        $this->assertStringContainsString('This is a list of all your configuration settings.', $output);
        $this->assertStringContainsString('display_errors', $output);
    }

    public function testPath(): void
    {
        $output = $this->captureHelperOutput(\Krumo::path(...));

        $this->assertStringContainsString('This is a list of the specified directories under your include_path option.', $output);
        $this->assertNotEmpty($output);
    }

    public function testRequest(): void
    {
        $output = $this->captureHelperOutput(\Krumo::request(...));

        $this->assertStringContainsString('$_REQUEST', $output);
        $this->assertStringContainsString('array (', $output);
    }

    public function testGet(): void
    {
        $output = $this->captureHelperOutput(\Krumo::get(...));

        $this->assertStringContainsString('$_GET', $output);
        $this->assertStringContainsString('array (', $output);
    }

    public function testPost(): void
    {
        $output = $this->captureHelperOutput(\Krumo::post(...));

        $this->assertStringContainsString('$_POST', $output);
        $this->assertStringContainsString('array (', $output);
    }

    public function testServer(): void
    {
        $output = $this->captureHelperOutput(\Krumo::server(...));

        $this->assertStringContainsString('$_SERVER', $output);
        $this->assertNotEmpty($output);
    }

    public function testCookie(): void
    {
        $output = $this->captureHelperOutput(\Krumo::cookie(...));

        $this->assertStringContainsString('$_COOKIE', $output);
        $this->assertStringContainsString('array (', $output);
    }

    public function testEnv(): void
    {
        if (!isset($_ENV)) {
            $this->markTestSkipped('$_ENV is not populated on this system');
        }

        $output = $this->captureHelperOutput(\Krumo::env(...));

        $this->assertStringContainsString('$_ENV', $output);
        $this->assertNotEmpty($output);
    }

    public function testSession(): void
    {
        if (!function_exists('session_start')) {
            $this->markTestSkipped('session extension is not available');
        }

        if (session_status() !== PHP_SESSION_ACTIVE) {
            @session_start();
        }
        if (!isset($_SESSION)) {
            $this->markTestSkipped('a session could not be started');
        }

        $output = $this->captureHelperOutput(\Krumo::session(...));

        $this->assertStringContainsString('$_SESSION', $output);
        $this->assertNotEmpty($output);
    }

    public function testIniDumpsValuesFromGivenFile(): void
    {
        $file = tempnam(sys_get_temp_dir(), 'krumo-ini-');
        file_put_contents($file, "[group_one]\ngreeting = \"krumo_ini_greeting\"\n");

        try {
            $output = $this->captureHelperOutput(fn() => \Krumo::ini($file));

            $this->assertStringContainsString('group_one', $output);
            $this->assertStringContainsString('krumo_ini_greeting', $output);
        } finally {
            @unlink($file);
        }
    }

    public function testIniWithUnreadableFileOutputsNothing(): void
    {
        $output = $this->captureHelperOutput(fn() => \Krumo::ini('/nonexistent/krumo-test.ini'));

        $this->assertSame('', trim($output));
    }

    public function testHelpersAreNoOpWhenDisabled(): void
    {
        \Krumo::disable();

        $output = $this->captureHelperOutput(\Krumo::classes(...));

        $this->assertSame('', trim($output));
    }
}
