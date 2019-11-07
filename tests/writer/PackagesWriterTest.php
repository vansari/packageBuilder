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
        $pathToTest = realpath(__DIR__ . '/../');
        $packageContainer = new PackageContainer();
        $packageContainer->addPackageFiles(
            [
                'foo\bar' => $pathToTest . '/foo/bar/package.php',
                'foo\bar\baz' => $pathToTest . '/foo/bar/baz/package.php',
                'foo\bar\buz' => $pathToTest . '/foo/bar/buz/package.php',
                'foo' => $pathToTest . '/foo/package.php',
            ]
        );

        $packagesWriter = new PackagesWriter(new WriterOptions(false, true, false, false));
        $result = $packagesWriter
            ->writePackagesFile(
                $pathToTest,
                $packageContainer
            );

        $this->assertSame(
            $pathToTest . '/packages.php',
            $result
        );
        $expectedResult = [
            $pathToTest . '/packages.php'
            => "<?php
declare (strict_types = 1);

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
