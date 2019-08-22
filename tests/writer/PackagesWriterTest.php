<?php
declare(strict_types = 1);

namespace tools\packageBuilder\writer;

use PHPUnit\Framework\TestCase;
use tools\packageBuilder\util\PackageContainer;

/**
 * Class PackagesWriterTest
 * @package tools\packageBuilder\writer
 * @coversDefaultClass \tools\packageBuilder\writer\PackagesWriter
 */
class PackagesWriterTest extends TestCase {

    /**
     * @covers ::writePackagesFile
     */
    public function testWritePackagesFilesDryRun(): void {
        $packageContainer = new PackageContainer();
        $packageContainer->addPackageFiles(
            [
                'foo\bar' => '/Volumes/GIT_Projects/foo-packagebuilder/tests/foo/bar/package.php',
                'foo\bar\baz' => '/Volumes/GIT_Projects/foo-packagebuilder/tests/foo/bar/baz/package.php',
                'foo\bar\buz' => '/Volumes/GIT_Projects/foo-packagebuilder/tests/foo/bar/buz/package.php',
                'foo' => '/Volumes/GIT_Projects/foo-packagebuilder/tests/foo/package.php',
            ]
        );

        $packagesWriter = new PackagesWriter(new WriterOptions(false, true, false, false));
        $result = $packagesWriter
            ->writePackagesFile(
                '/Volumes/GIT_Projects/foo-packagebuilder/tests/',
                $packageContainer
            );

        $this->assertSame(
            '/Volumes/GIT_Projects/foo-packagebuilder/tests/packages.php',
            $result
        );
        $expectedResult = [
            '/Volumes/GIT_Projects/foo-packagebuilder/tests/packages.php'
            => "<?php
declare(strict_types = 1);

return [
    'foo' => __DIR__ . '/foo/package.php',
    'foo\bar' => __DIR__ . '/foo/bar/package.php',
    'foo\bar\baz' => __DIR__ . '/foo/bar/baz/package.php',
    'foo\bar\buz' => __DIR__ . '/foo/bar/buz/package.php',
];"
        ];
        $this->assertEquals(
            $expectedResult,
            $packagesWriter->getDryRunResult()
        );
    }
}
