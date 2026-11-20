<?php

declare(strict_types=1);

namespace Krumo\Tests;

final class SkinTest extends BaseTestCase
{
    public function testCliOutputContainsSeparator(): void
    {
        $output = $this->getCliOutput(['key' => 'value']);

        $this->assertStringContainsString('key', $output);
        $this->assertStringContainsString('value', $output);
    }

    public function testModernSkinCssIsUsed(): void
    {
        [$output, $exitCode] = $this->runPhpScript(
            'Krumo::setConfig(["skin" => ["selected" => "modern"]]);'
            . ' (new ReflectionMethod("Krumo", "_css"))->invoke(null);'
        );

        $this->assertSame(0, $exitCode);
        $this->assertStringContainsString('Using Krumo Skin: "modern"', $output);
        $this->assertStringContainsString('<style>', $output);
        $this->assertStringContainsString('<script>', $output);
    }

    public function testUnknownSkinFallsBackToStylish(): void
    {
        [$output, $exitCode] = $this->runPhpScript(
            'Krumo::setConfig(["skin" => ["selected" => "nonexistent"]]);'
            . ' (new ReflectionMethod("Krumo", "_css"))->invoke(null);'
        );

        $this->assertSame(0, $exitCode);
        $this->assertStringContainsString('Using Krumo Skin: "stylish"', $output);
        $this->assertStringContainsString('<style>', $output);
    }

    public function testMissingDefaultSkinDoesNotWarn(): void
    {
        // Simulate an installation where skins/ is not next to the class
        // file (partial deploy, relocated library): the fallback read used
        // to emit a file_get_contents() warning on every dump.
        $tempDir = sys_get_temp_dir() . '/krumo-skin-' . uniqid();
        mkdir($tempDir);
        $tempClass = $tempDir . '/class.krumo.php';
        copy(dirname(__DIR__, 2) . '/class.krumo.php', $tempClass);

        try {
            [$output, $exitCode] = $this->runPhpScript(
                '(new ReflectionMethod("Krumo", "_css"))->invoke(null);'
                . ' echo "CSS_DONE";',
                PHP_BINARY,
                '-d display_errors=1 -d error_reporting=E_ALL',
                $tempClass
            );
        } finally {
            @unlink($tempClass);
            @rmdir($tempDir);
        }

        $this->assertSame(0, $exitCode);
        $this->assertStringContainsString('CSS_DONE', $output);
        $this->assertStringNotContainsString('Warning', $output);
        $this->assertStringNotContainsString('<style>', $output);
    }

    public function testSkinUrlPlaceholderIsReplaced(): void
    {
        [$output, $exitCode] = $this->runPhpScript(
            'Krumo::setConfig(["skin" => ["selected" => "blue"]]);'
            . ' (new ReflectionMethod("Krumo", "_css"))->invoke(null);'
        );

        $this->assertSame(0, $exitCode);
        $this->assertStringContainsString('Using Krumo Skin: "blue"', $output);
        // DOCUMENT_ROOT is the project root, so the relative CSS URL is "/"
        // and the %url% placeholder resolves to "/skins/blue/"
        $this->assertStringContainsString('/skins/blue/', $output);
        $this->assertStringNotContainsString('%url%', $output);
    }

    public function testKrumoVersionInCliOutput(): void
    {
        \Krumo::setConfig(['display' => ['show_version' => true]]);

        $output = $this->getCliOutput('test');

        $this->assertStringContainsString('Krumo version', $output);
    }

    public function testShowCallInfoInCliOutput(): void
    {
        \Krumo::setConfig(['display' => ['show_call_info' => true]]);

        $output = $this->getCliOutput('test');

        $this->assertStringContainsString('Called from', $output);
    }
}
