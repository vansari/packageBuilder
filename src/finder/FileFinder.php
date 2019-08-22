<?php
declare(strict_types = 1);

namespace tools\packageBuilder\finder;

use InvalidArgumentException;
use RecursiveDirectoryIterator;
use SplFileInfo;

/**
 * Class FileFinder - such und findet alle php Dateien in einem angegebenen Verzeichnis
 * @package tools\packageBuilder\finder
 */
class FileFinder {
    /** @var array $ignoredFilenames */
    private $ignoredFilenames = [];

    /** @var array $files */
    private $files = [];

    /**
     * Prüft ob $fileInfo eine Php Datei ist und hängt diese dann an $files an
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
     * Sucht anhand des Verzeichnisses die vorhandenen *.php Dateien
     * und gibt das Ergebnis als array zurück
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
     * Funktion um Dateien zu suchen und mit den zu ignorierende abzugleichen
     * Befüllt $this->files mit den korrekten php Dateien
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
                    $this->ignoredFilenames
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
     * @param array $ignoredFilenames
     *
     * @return $this
     */
    public function setIgnoredFilenames(array $ignoredFilenames): self {
        foreach ($ignoredFilenames as $ignoredFilename) {
            $this->addIgnoredFilename($ignoredFilename);
        }

        return $this;
    }

    public function addIgnoredFilename(string $file): self {
        $this->claimPhpExtension($file);
        $this->ignoredFilenames[] = $file;

        return $this;
    }
}