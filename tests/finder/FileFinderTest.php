<?php
declare(strict_types = 1);

namespace tools\packageBuilder\finder;

use PHPUnit\Framework\TestCase;
use SebastianBergmann\CodeCoverage\Node\File;

/**
 * Class FileFinderTest
 * @package tools\packageBuilder\finder
 * @coversDefaultClass \tools\packageBuilder\finder\FileFinder
 */
class FileFinderTest extends TestCase {

    /**
     * @var FileFinder
     */
    private $fileFinder;

    public function setUp(): void {
        $this->fileFinder = new FileFinder();
    }

    /**
     * @covers ::findFiles
     */
    public function testFindFiles(): void {
        $this->assertCount(5, $this->fileFinder->findFiles(__DIR__ . '/../testclasses/'));
    }

    public function testFindFilesRecursive(): void {
        $this->assertCount(7, $this->fileFinder->findFiles(__DIR__ . '/../testclasses/', true));
    }
    /**
     * @covers ::getFiles
     */
    public function testGetFiles(): void {
        $this->fileFinder->findFiles(__DIR__ . '/../testclasses/', true);
        $this->assertCount(7, $this->fileFinder->getFiles());
        $this->fileFinder = new FileFinder();
        $this->assertCount(0, $this->fileFinder->getFiles());
    }

    /**
     * @covers ::setFiles
     */
    public function testSetFiles(): void {
        $this->fileFinder->findFiles(__DIR__ . '/../testclasses/', true);
        $this->assertCount(7, $this->fileFinder->getFiles());
        $this->fileFinder->setFiles([__DIR__ . '/../testclasses/TestClassOne.php']);
        $this->assertCount(1, $this->fileFinder->getFiles());
    }
}
