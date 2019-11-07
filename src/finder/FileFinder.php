<?php
declare(strict_types = 1);

namespace tools\packageBuilder\finder;

use InvalidArgumentException;
use RecursiveDirectoryIterator;
use SplFileInfo;

/**
 * Class FileFinder - search and find all php files in the given path
 * @package tools\packageBuilder\finder
 */
class FileFinder {
    /** @var array $ignoredFilenames */
    private $ignoredFilenames = [];

    /** @var array $files */
    private $files = [];

    /**
     * Check whether $fileInfo is an php file and append them
     * @param SplFileInfo $fileInfo
     * @return string|null
     */
    private function fetchPhpClassFiles(SplFileInfo $fileInfo): ?string {
        if (
            $fileInfo->isFile()
            || 'php' === pathinfo($fileInfo->getPathname(), PATHINFO_EXTENSION)
        ) {
            return $fileInfo->getPathname();
        }

        return null;
    }

    /**
     * Find the existing *.php files from the directory and return the result as an array
     *
     * @param string $directory
     * @param bool $recursive - default false
     *
     * @return array
     */
    public function findFiles(string $directory, bool $recursive = false): array {
        $this->claimDirectory($directory);
        $this->fetchMatchedFiles(realpath($directory), $recursive);

        return $this->getFiles();
    }

    /**
     *
     * Function to search files and match them with those to ignore
     * Fill $this->files with the correct php files
     *
     * @param string $path
     * @param bool $recursive
     */
    private function fetchMatchedFiles(string $path, bool $recursive = false): void {
        $files = [];
        $recursiveIterator = new RecursiveDirectoryIterator($path);
        $recursiveIterator->rewind();
        while ($recursiveIterator->valid()) {
            if (($recursiveIterator->isDir() && !$recursiveIterator->isDot()) && $recursive) {
                $this->fetchMatchedFiles($recursiveIterator->getPathname(), $recursive);
                $recursiveIterator->next();
                continue;
            }
            if (
                in_array(
                    pathinfo(
                        $recursiveIterator->getFilename(),
                        PATHINFO_BASENAME
                    ),
                    $this->getIgnoredFilenames()
                )
            ) {
                $recursiveIterator->next();
                continue;
            }
            if (null === ($file = $this->fetchPhpClassFiles($recursiveIterator->current()))) {
                $recursiveIterator->next();
                continue;
            }
            $files[] = $file;
            $recursiveIterator->next();
        }
        $this->setFiles(array_merge($this->getFiles(), $files));
    }

    /**
     * @param string $directory
     *
     * @return $this
     */
    private function claimDirectory(string $directory): void {
        if ('' !== $directory && is_dir($directory)) {
            return;
        }

        throw new InvalidArgumentException('Path "' . $directory . '" is not a directory.');
    }

    /**
     * @return array
     */
    public function getFiles(): array {
        return $this->files;
    }

    /**
     * @param array $files
     *
     * @return $this
     */
    public function setFiles(array $files): self {
        $this->files = $files;

        return $this;
    }

    /**
     * @return array
     */
    public function getIgnoredFilenames(): array {
        return $this->ignoredFilenames;
    }

    private function claimPhpExtension(string $file): void {
        if ('' === $file || 'php' !== pathinfo($file, PATHINFO_EXTENSION)) {
            throw new InvalidArgumentException('Only php files accepted.');
        }

        return;
    }

    /**
     * Sets a new array of ignored Files
     * @param array $ignoredFilenames
     * @return $this
     */
    public function setIgnoredFilenames(array $ignoredFilenames): self {
        array_walk($ignoredFilenames, [$this, 'claimPhpExtension']);
        $this->ignoredFilenames[] = $ignoredFilenames;

        return $this;
    }

    /**
     * append an array of ignored Files to the existing collection
     * @param array $ignoredFilenames
     * @return $this
     */
    public function appendIgnoredFilenames(array $ignoredFilenames): self {
        foreach ($ignoredFilenames as $ignoredFilename) {
            $this->addIgnoredFilename($ignoredFilename);
        }

        return $this;
    }

    /**
     * adds a new ignored filename
     * @param string $file
     * @return $this
     */
    public function addIgnoredFilename(string $file): self {
        $this->claimPhpExtension($file);
        $this->ignoredFilenames[] = $file;

        return $this;
    }
}