<?php
declare(strict_types = 1);

namespace tools\packageBuilder;

use PHPUnit\Framework\TestCase;
use tools\packageBuilder\writer\WriterOptions;

/**
 * Class PackageBuilderTest
 * @package tools\packageBuilder
 * @coversDefaultClass \tools\packageBuilder\PackageBuilder
 */
class PackageBuilderTest extends TestCase {
    /**
     * @covers ::buildFiles
     */
    public function testBuildFiles(): void {
        $builder = PackageBuilder::create(__DIR__ . '/testclasses/')
            ->setWriterOptions(new WriterOptions(true, true, true, false))
            ->setIgnoredFilenames(['package.php', 'packages.php'])
            ->setWithPackagesFile(true);

        $builder->buildFiles();
        $packagesFile = $builder->getDryRunResultPackagesFile();
        $packagesFileContent = "<?php
declare(strict_types = 1);

return [
    'tests\packageBuilder\\testclasses' => __DIR__ . '/package.php',
    'tests\packageBuilder\\testclasses\subdirectory' => __DIR__ . '/subdirectory/package.php',
];";
        $this->assertSame(
            $packagesFileContent,
            $packagesFile['/Volumes/GIT_Projects/packageBuilder/tests/testclasses/packages.php']
        );
        $this->assertCount(1, $packagesFile);

        $packageFiles = $builder->getDryRunResultPackageFiles();
        $packageFileInSubdirectoryDir = "<?php
declare(strict_types = 1);

namespace tests\packageBuilder\\testclasses\subdirectory;

return [
    'TestAbstractClass' => 'TestAbstractClass.php',
    'TestClassTwo' => 'TestClassTwo.php',
];";

        $packageFileInTestClassesDir = "<?php
declare(strict_types = 1);

namespace tests\packageBuilder\\testclasses;

return [
    'TestAbstractClass' => 'TestAbstractClass.php',
    'TestClassFactory' => 'TestClassFactory.php',
    'TestClassInterface' => 'TestClassInterface.php',
    'TestClassOne' => 'TestClassOne.php',
    'TestClassTrait' => 'TestClassTrait.php',
];";
        $this->assertCount(2, $packageFiles);

        $this->assertSame(
            $packageFileInSubdirectoryDir,
            $packageFiles[0]['/Volumes/GIT_Projects/packageBuilder/tests/testclasses/subdirectory/package.php']
        );

        $this->assertSame(
            $packageFileInTestClassesDir,
            $packageFiles[1]['/Volumes/GIT_Projects/packageBuilder/tests/testclasses/package.php']
        );
    }
}
