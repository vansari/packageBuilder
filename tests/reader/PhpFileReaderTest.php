<?php
declare(strict_types = 1);

namespace tools\packageBuilder\reader;

use PHPUnit\Framework\TestCase;

/**
 * Class PhpFileReaderTest
 * @package tools\packageBuilder\reader
 * @coversDefaultClass \tools\packageBuilder\reader\PhpFileReader
 */
class PhpFileReaderTest extends TestCase {

    /**
     * @var PhpFileReader
     */
    private $reader;

    public function setUp(): void {
        $this->reader = new PhpFileReader();
    }

    /**
     * @covers PhpFileReader::readContent
     */
    public function testReadContent(): void {
        $expectedContent = '<?php
declare(strict_types = 1);

namespace tests\packageBuilder\testclasses;

class TestClassOne implements TestClassInterface {
    use TestClassTrait;

    private $property = null;

    public function __construct(string $property) {
        $this->property = $property;
    }

    /**
     * @return string|null
     */
    public function getProperty(): ?string {
        return $this->property;
    }

    /**
     * @param string|null $property
     *
     * @return $this
     */
    public function setProperty(?string $property): self {
        $this->property = $property;

        return $this;
    }

}';
        $file = __DIR__ . '/../testclasses/TestClassOne.php';
        $content = $this->reader->readContent($file);
        $this->assertNotEmpty($content);
        $this->assertEquals($expectedContent, $content);
    }

    /**
     * @covers PhpFileReader::getTokens
     */
    public function testGetTokens(): void {
        $file = __DIR__ . '/../testclasses/TestClassOne.php';
        $tokens = $this->reader->getTokens($file);
        $this->assertGreaterThan(0, $tokens);
    }

    /**
     * @covers PhpFileReader::fetchClass
     */
    public function testFetchClass(): void {
        $result = $this->reader->fetchClass(__DIR__ . '/../testclasses/TestClassOne.php');
        $this->assertNotEmpty($result);
        $this->assertSame('tests\packageBuilder\testclasses', $result[0]);
        $this->assertSame(['TestClassOne' => 'TestClassOne.php'], $result[1]);
    }

    public function testFetchAbstractClass(): void {
        $result = $this->reader->fetchClass(__DIR__ . '/../testclasses/TestAbstractClass.php');
        $this->assertNotEmpty($result);
        $this->assertSame('tests\packageBuilder\testclasses', $result[0]);
        $this->assertSame(['TestAbstractClass' => 'TestAbstractClass.php'], $result[1]);
    }
}
