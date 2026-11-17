<?php

declare(strict_types=1);

namespace Krumo\Tests;

final class RelativePathTest extends BaseTestCase
{
    private const DOCUMENT_ROOT = '/var/www/site';

    private ?string $originalDocumentRoot = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this->originalDocumentRoot = $_SERVER['DOCUMENT_ROOT'] ?? null;
        $_SERVER['DOCUMENT_ROOT'] = self::DOCUMENT_ROOT;
    }

    protected function tearDown(): void
    {
        if ($this->originalDocumentRoot === null) {
            unset($_SERVER['DOCUMENT_ROOT']);
        } else {
            $_SERVER['DOCUMENT_ROOT'] = $this->originalDocumentRoot;
        }

        parent::tearDown();
    }

    public function testReturnsPathRelativeToDocumentRoot(): void
    {
        $this->assertSame(
            '/app/index.php',
            \Krumo::calculate_relative_path(self::DOCUMENT_ROOT . '/app/index.php')
        );
    }

    public function testFileDirectlyInDocumentRoot(): void
    {
        $this->assertSame(
            '/index.php',
            \Krumo::calculate_relative_path(self::DOCUMENT_ROOT . '/index.php')
        );
    }

    public function testReturnDirReturnsOnlyTheDirectory(): void
    {
        $this->assertSame(
            '/app/',
            \Krumo::calculate_relative_path(self::DOCUMENT_ROOT . '/app/index.php', true)
        );
    }

    public function testDuplicateSlashesCollapse(): void
    {
        $this->assertSame(
            '/lib/x.php',
            \Krumo::calculate_relative_path(self::DOCUMENT_ROOT . '//lib/x.php')
        );
    }

    public function testPathOutsideDocumentRootReturnsFalse(): void
    {
        $this->assertFalse(\Krumo::calculate_relative_path('/somewhere/else/x.php'));
    }

    public function testMissingDocumentRootReturnsFalseWithoutWarnings(): void
    {
        unset($_SERVER['DOCUMENT_ROOT']);

        set_error_handler(static function (int $severity): never {
            throw new \ErrorException('Unexpected PHP warning', 0, $severity);
        });

        try {
            $this->assertFalse(\Krumo::calculate_relative_path('/var/www/site/app/index.php'));
        } finally {
            restore_error_handler();
        }
    }
}
