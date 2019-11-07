<?php
declare(strict_types = 1);

namespace tools\packageBuilder\util;

use Countable;
use Iterator;

/**
 * Class PackageContainer
 * @package tools\packageBuilder\util
 */
class PackageContainer implements Countable, Iterator {

    /**
     * @var array
     */
    private $container = [];

    /**
     * Add a new package file for the given namespace if it not exists in the container
     * @param string $namespace
     * @param string $packagePath
     * @return $this
     */
    public function addPackageFile(string $namespace, string $packagePath): self {
        if (false === array_key_exists($namespace, $this->container)) {
            $this->container[$namespace] = $packagePath;
        }

        return $this;
    }

    /**
     * add a array of package files to the existing container if they did not exists
     * @param array $packageFiles
     * @return $this
     *
     * @uses \tools\packageBuilder\util\PackageContainer::addPackageFile()
     */
    public function addPackageFiles(array $packageFiles): self {
        foreach ($packageFiles as $namespace => $packageFile) {
            if (false === is_string($namespace) && '' === $namespace) {
                continue;
            }
            $this->addPackageFile($namespace, $packageFile);
        }

        return $this;
    }

    /**
     * Clearing the current container and add the package files
     * @param array $packageFiles
     * @return $this
     */
    public function setPackageFiles(array $packageFiles): self {
        $this->container = [];
        $this->addPackageFiles($packageFiles);

        return $this;
    }

    /**
     * Returns all available raw data in this container
     * @return array
     */
    public function getRawData(): array {
        return $this->container;
    }

    /**
     * Returns all package file which existing with the given namespace
     * @param string $value
     * @return array
     */
    public function getByNamespace(string $value): array {
        foreach ($this as $namespace => $path) {
            if ($value !== $namespace) {
                continue;
            }
            return [$namespace => $path];
        }

        return [];
    }

    /**
     * Returns all package files which existing with the given path
     * @param string $value
     * @return array
     */
    public function getByPath(string $value): array {
        foreach ($this as $namespace => $path) {
            if ($value !== $path) {
                continue;
            }
            return [$namespace => $path];
        }

        return [];
    }

    /**
     * @return bool
     */
    public function isEmpty(): bool {
        return [] === $this->container;
    }

    /**
     * Return the current element
     * @link https://php.net/manual/en/iterator.current.php
     * @return mixed Can return any type.
     * @since 5.0.0
     */
    public function current() {
        return current($this->container);
    }

    /**
     * Move forward to next element
     * @link https://php.net/manual/en/iterator.next.php
     * @return void Any returned value is ignored.
     * @since 5.0.0
     */
    public function next(): void {
        next($this->container);
    }

    /**
     * Return the key of the current element
     * @link https://php.net/manual/en/iterator.key.php
     * @return mixed scalar on success, or null on failure.
     * @since 5.0.0
     */
    public function key() {
        return key($this->container);
    }

    /**
     * Checks if current position is valid
     * @link https://php.net/manual/en/iterator.valid.php
     * @return boolean The return value will be casted to boolean and then evaluated.
     * Returns true on success or false on failure.
     * @since 5.0.0
     */
    public function valid() {
        return false !== $this->current();
    }

    /**
     * Rewind the Iterator to the first element
     * @link https://php.net/manual/en/iterator.rewind.php
     * @return void Any returned value is ignored.
     * @since 5.0.0
     */
    public function rewind() {
        reset($this->container);
    }

    /**
     * Count elements of an object
     * @link https://php.net/manual/en/countable.count.php
     * @return int The custom count as an integer.
     * </p>
     * <p>
     * The return value is cast to an integer.
     * @since 5.1.0
     */
    public function count(): int {
        return count($this->container);
    }

    /**
     * Sort the Packages by key with ignoring case
     * @return $this
     */
    public function sortPackages(): self {
        ksort($this->container, SORT_STRING | SORT_FLAG_CASE);

        return $this;
    }

    /**
     * sort the container by key reverse with ignoring case
     * @return $this
     */
    public function sortPackagesReverse(): self {
        krsort($this->container, SORT_STRING | SORT_FLAG_CASE);

        return $this;
    }
}