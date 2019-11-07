<?php
declare(strict_types = 1);

namespace tools\packageBuilder\writer;

use PHPUnit\Framework\TestCase;
use tools\packageBuilder\finder\FileFinder;
use tools\packageBuilder\sorter\ClassFileSorter;
use tools\packageBuilder\util\ClassHolder;
use tools\packageBuilder\util\ClassHolderContainer;
use tools\packageBuilder\util\PackageContainer;

/**
 * Class PackageWriterTest
 * @package tools\packageBuilder\writer
 * @coversDefaultClass \tools\packageBuilder\writer\PackageWriter
 */
class PackageWriterTest extends TestCase {

    public const TESTS_PATH = __DIR__ . '/../';
    /**
     * @covers ::writePackageFile
     * @testdox Tests a generation of a Package File with one PHP ClassFile
     */
    public function testWritePackageFileDryRunModeWithOneFile(): void {
        $testDir = __DIR__ . '/../testclasses/subdirectory';
        $fileFinder = new FileFinder();
        $phpFiles = $fileFinder->findFiles($testDir, false);
        $this->assertCount(2, $phpFiles);
        /** @var ClassHolderContainer $sortedClasses */
        $sortedClasses = ClassFileSorter::getSortedClassHolderContainer($phpFiles);
        $this->assertNotEmpty($sortedClasses);
        /** @var ClassHolder $classHolder */
        foreach ($sortedClasses as $classHolder) {
            $writer = new PackageWriter(new WriterOptions(false, true));
            $packagePath = $writer->writePackageFile($classHolder);
            $this->assertSame('tests\packageBuilder\testclasses\subdirectory', $writer->getPackageNamespace());
            $this->assertTrue(false !== strpos($packagePath,'tests/testclasses/subdirectory/package.php'));
        }
    }

    /**
     * @throws \Exception
     * @covers ::writePackageFile
     * @testdox Tests a generation of a Package File with many PHP ClassFiles
     */
    public function testWritePackageFileDryRunModeWithMoreFiles(): void {
        $testDir = __DIR__ . '/../testclasses';
        $fileFinder = new FileFinder();
        $files = $fileFinder->findFiles($testDir, false);
        $this->assertCount(5, $files);
        /** @var ClassHolderContainer $sortedClasses */
        $sortedClassHolder = ClassFileSorter::getSortedClassHolderContainer($files);
        $this->assertNotEmpty($sortedClassHolder);
        /** @var ClassHolder $classHolder */
        foreach ($sortedClassHolder as $classHolder) {
            $writer = new PackageWriter(new WriterOptions(false, true));
            $packagePath = $writer->writePackageFile($classHolder);
            $this->assertSame('tests\packageBuilder\testclasses', $writer->getPackageNamespace());
            $this->assertTrue(false !== strpos($packagePath,'tests/testclasses/package.php'));
        }
    }

    /**
     * @throws \Exception
     * @covers ::writePackageFiles
     * @testdox Tests a generation of more than one packageFile
     */
    public function testWritePackageFiles(): void {
        $testDir = __DIR__ . '/../testclasses';
        $fileFinder = new FileFinder();
        $files = $fileFinder->findFiles($testDir, true);
        $this->assertCount(7, $files);
        /** @var ClassHolderContainer $sortedClasses */
        $sortedClassHolder = ClassFileSorter::getSortedClassHolderContainer($files);
        $this->assertNotEmpty($sortedClassHolder);
        /** @var PackageContainer $result */
        $result = (new PackageWriter(new WriterOptions(false, true)))->writePackageFiles($sortedClassHolder);
        $expected = [
            'tests\packageBuilder\testclasses'
            => 'tests/testclasses/package.php',
            'tests\packageBuilder\testclasses\subdirectory'
            => 'tests/testclasses/subdirectory/package.php',
        ];
        foreach ($expected as $namespace => $packagePath) {
            $this->assertTrue(array_key_exists($namespace, $result->getRawData()));
        }
    }
}
