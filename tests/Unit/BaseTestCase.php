<?php

declare(strict_types=1);

namespace Krumo\Tests;

use PHPUnit\Framework\TestCase;
use ReflectionMethod;

abstract class BaseTestCase extends TestCase
{
    /**
     * Non-empty "sentinel" config: Krumo re-reads krumo.ini whenever its
     * config is empty, so tests must never hold an empty config. No section
     * key matches what Krumo looks up, so every _config() call falls back
     * to its default and no test depends on a local krumo.ini.
     */
    private const HERMETIC_CONFIG = [
        'skin' => [],
        'display' => [],
        'sorting' => [],
    ];

    protected function setUp(): void
    {
        \Krumo::enable();
        \Krumo::setConfig(self::HERMETIC_CONFIG);
        \Krumo::$expand_all = 0;
        \Krumo::$sort = null;
        \Krumo::cascade(null);
    }

    /**
     * Render HTML via the internal _dump() method, bypassing CLI mode check.
     */
    protected function renderHtml(mixed $value): string
    {
        $ref = new ReflectionMethod(\Krumo::class, '_dump');

        ob_start();
        $ref->invokeArgs(null, [&$value]);
        return ob_get_clean();
    }

    /**
     * Get CLI output from dump().
     */
    protected function getCliOutput(mixed ...$args): string
    {
        ob_start();
        foreach ($args as $data) {
            \Krumo::dump($data);
        }
        return ob_get_clean();
    }

    /**
     * Run a PHP snippet in a separate process, with class.krumo.php loaded
     * and the same hermetic config used in-process.
     *
     * The snippet runs with $_SERVER['DOCUMENT_ROOT'] pointing at the
     * project root, so Krumo can resolve its skin URLs without warnings.
     *
     * @return array{0: string, 1: int} combined stdout/stderr and exit code
     */
    protected function runPhpScript(string $body, ?string $phpBinary = null, string $cliOptions = ''): array
    {
        $root = dirname(__DIR__, 2);

        $script = '<?php '
            . '$_SERVER["DOCUMENT_ROOT"] = ' . var_export($root, true) . '; '
            . 'require ' . var_export($root . '/class.krumo.php', true) . '; '
            . 'Krumo::setConfig(' . var_export(self::HERMETIC_CONFIG, true) . '); '
            . $body;

        $file = tempnam(sys_get_temp_dir(), 'krumo-test-');
        file_put_contents($file, $script);

        $binary = $phpBinary ?? PHP_BINARY;
        $command = sprintf('%s %s %s 2>&1', escapeshellarg($binary), $cliOptions, escapeshellarg($file));

        $output = [];
        $exitCode = 0;
        exec($command, $output, $exitCode);

        @unlink($file);

        return [implode("\n", $output), $exitCode];
    }

    /**
     * Path to a CGI-capable PHP binary (needed to exercise Krumo's HTML
     * output, since dump() falls back to var_export() in CLI mode), or null
     * when none is available.
     */
    protected static function phpCgiBinary(): ?string
    {
        static $cached = false;

        if ($cached !== false) {
            return $cached;
        }

        $candidate = PHP_BINARY . '-cgi';
        if (is_executable($candidate)) {
            $cached = $candidate;

            return $candidate;
        }

        $output = [];
        $exitCode = 1;
        exec('command -v php-cgi 2>/dev/null', $output, $exitCode);
        if ($exitCode === 0 && $output !== []) {
            $cached = (string) $output[0];

            return $cached;
        }

        $cached = null;

        return null;
    }
}
