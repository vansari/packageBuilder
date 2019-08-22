<?php
declare(strict_types = 1);

namespace tools\packageBuilder\util;

use Countable;
use Iterator;

class ClassHolderContainer implements Countable, Iterator {

    /**
     * @var array
     */
    private $container = [];

    public function addClassHolder(ClassHolder $classHolder): self {
        if (in_array($classHolder, $this->container, true)) {
            throw new InvalidArgumentException('ClassHolder already exists in Container!');
        }
        $this->container[] = $classHolder;

        return $this;
    }

    public function getClassHolders(): array {
        return $this->container;
    }

    public function getClassHolderByNamespace(string $namespace): ?ClassHolder {
        foreach ($this as $classHolder) {
            if ($classHolder->getNamespace() === $namespace) {
                return $classHolder;
            }
        }

        return null;
    }

    public function isEmpty(): bool {
        return 0 === $this->container;
    }

    public function removeClassHolderByNamespace(string $namespace): self {
        foreach ($this as $index => $classHolder) {
            if ($classHolder->getNamespace() !== $namespace) {
                continue;
            }
            unset($this->container[$index]);
        }

        return $this;
    }

    public function getClassHolderByPath(string $path): ?ClassHolder {
        foreach ($this as $classHolder) {
            if ($classHolder->getPath() === $path) {
                return $classHolder;
            }
        }

        return null;
    }

    public function removeClassHolderByPath(string $path): self {
        foreach ($this as $index => $classHolder) {
            if ($classHolder->getPath() !== $path) {
                continue;
            }
            unset($this->container[$index]);
        }

        return $this;
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
}