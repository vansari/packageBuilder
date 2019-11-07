<?php
declare(strict_types = 1);

namespace tools\packageBuilder;

use tools\packageBuilder\finder\FileFinder;
use tools\packageBuilder\sorter\ClassFileSorter;
use tools\packageBuilder\util\ClassHolderContainer;
use tools\packageBuilder\util\PackageContainer;
use tools\packageBuilder\writer\PackagesWriter;
use tools\packageBuilder\writer\PackageWriter;
use tools\packageBuilder\writer\WriterOptions;

class PackageBuilder {
    /**
     * @var PackageContainer
     */
    private $packageContainer;
    /**
     * @var string|null
     */
    private $packagesFilePath;

    /**
     * @var string
     */
    private $path;

    /** @var WriterOptions */
    private $writerOptions;
    /** @var bool $withPackagesFile */
    private $withPackagesFile = false;
    /** @var array $ignoredFilenames */
    private $ignoredFilenames = [];
    /** @var array $dryRunResultPackageFiles */
    private $dryRunResultPackageFiles = [];
    /** @var array $dryRunResultPackagesFile */
    private $dryRunResultPackagesFile = [];

    /**
     * PackageBuilder constructor.
     * @param string $path - The root path where the PackagerBuilder should begin
     */
    public function __construct(string $path) {
        if ('' === $path) {
            throw new \InvalidArgumentException('Der Pfad muss angegeben werden!');
        }
        $this->path = $path;
    }

    /**
     * Static helper for a fancy start
     * @param string $path
     * @return static
     */
    public static function create(string $path): self {
        return new self($path);
    }

    /**
     * Starts the Process to collect and build the package.php files
     * Otional it creates also an packages.php file
     */
    public function buildFiles(): void {
        $packageContainer = $this->writePackageFiles($this->collectFiles());
        if ($this->withPackagesFile()) {
            $this->writePackagesFile($packageContainer);
        }
    }

    /**
     * @return array
     */
    public function getDryRunResultPackageFiles(): array {
        return $this->dryRunResultPackageFiles;
    }

    /**
     * @param string $packageFilePath
     * @return string
     */
    public function getDryRunPackageFileContent(string $packageFilePath): string {
        return $this->dryRunResultPackageFiles[$packageFilePath];
    }

    /**
     * @param array $dryRunResultPackageFiles
     *
     * @return $this
     */
    public function setDryRunResultPackageFiles(array $dryRunResultPackageFiles): self {
        $this->dryRunResultPackageFiles = $dryRunResultPackageFiles;

        return $this;
    }

    /**
     * @return array - The Result which the builder would write
     */
    public function getDryRunResultPackagesFile(): array {
        return $this->dryRunResultPackagesFile;
    }

    /**
     * @param array $dryRunResultPackagesFile
     *
     * @return $this
     */
    public function setDryRunResultPackagesFile(array $dryRunResultPackagesFile): self {
        $this->dryRunResultPackagesFile = $dryRunResultPackagesFile;

        return $this;
    }

    /**
     * @return WriterOptions
     */
    public function getWriterOptions(): WriterOptions {
        return $this->writerOptions ?? new WriterOptions();
    }

    /**
     * @param WriterOptions $writerOptions
     *
     * @return $this
     */
    public function setWriterOptions(WriterOptions $writerOptions): self {
        $this->writerOptions = $writerOptions;

        return $this;
    }

    /**
     * @return bool
     */
    public function withPackagesFile(): bool {
        return $this->withPackagesFile;
    }

    /**
     * @return array
     */
    public function getIgnoredFilenames(): array {
        return $this->ignoredFilenames;
    }

    /**
     * @param array $ignoredFilenames
     *
     * @return $this
     */
    public function setIgnoredFilenames(array $ignoredFilenames): self {
        $this->ignoredFilenames = $ignoredFilenames;

        return $this;
    }

    /**
     * @return PackageContainer
     */
    public function getPackageContainer(): PackageContainer {
        return $this->packageContainer ?? new PackageContainer();
    }

    /**
     * @return string|null
     */
    public function getPackagesFilePath(): ?string {
        return $this->packagesFilePath;
    }

    /**
     * @param bool $withPackagesFile
     *
     * @return $this
     */
    public function setWithPackagesFile(bool $withPackagesFile): self {
        $this->withPackagesFile = $withPackagesFile;

        return $this;
    }

    /**
     * Collect all Files in the starting root path and optional recursive
     * @return ClassHolderContainer - All php files which the builder has found
     */
    private function collectFiles(): ClassHolderContainer {
        $fileFinder = new FileFinder();
        $files = $fileFinder
            ->appendIgnoredFilenames($this->getIgnoredFilenames())
            ->findFiles($this->path, $this->getWriterOptions()->doRecursive());

        return ClassFileSorter::getSortedClassHolderContainer($files);
    }

    /**
     * @param ClassHolderContainer $container
     * @return PackageContainer
     * @throws \Exception
     */
    private function writePackageFiles(ClassHolderContainer $container): PackageContainer {
        $packageWriter = new PackageWriter($this->getWriterOptions());
        $packageContainer = $packageWriter
            ->writePackageFiles($container);

        if($this->getWriterOptions()->isDryRun()) {
            $this->setDryRunResultPackageFiles($packageWriter->getDryRunResult());
        }

        return ($this->packageContainer = $packageContainer);
    }

    /**
     * @param PackageContainer $container
     * @return string|null
     * @throws \Exception
     */
    private function writePackagesFile(PackageContainer $container): ?string {
        $packagesWriter = new PackagesWriter($this->getWriterOptions());

        $packagesFile = $packagesWriter
            ->writePackagesFile($this->path, $container);

        if ($this->getWriterOptions()->isDryRun()) {
            $this->setDryRunResultPackagesFile($packagesWriter->getDryRunResult());
        }

        return ($this->packagesFilePath = $packagesFile);
    }
}