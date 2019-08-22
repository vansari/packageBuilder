<?php
declare(strict_types = 1);

namespace tools\packageBuilder\util;

use InvalidArgumentException;

/**
 * Class ClassHolder - Hält alle Klassen die in einem Verzeichnis und Namespace existieren vor
 * @package tools\packageBuilder\util
 */
class ClassHolder {

    /**
     * @var string
     */
    private $path;

    private $namespace = null;

    private $classes = [];

    public function __construct(string $path) {
        if ('' === $path || false === is_dir($path)) {
            throw new InvalidArgumentException('Der Pfad existiert nicht!');
        }
        $this->path = $path;
    }

    /**
     * @return array
     */
    public function getClasses(): array {
        return $this->classes;
    }

    /**
     * @return null
     */
    public function getNamespace() {
        return $this->namespace;
    }

    /**
     * @param null $namespace
     *
     * @return $this
     */
    public function setNamespace($namespace): self {
        $this->namespace = $namespace;

        return $this;
    }

    /**
     * @param array $classes
     *
     * @return $this
     */
    public function setClasses(array $classes): self {
        $this->classes = $classes;

        return $this;
    }

    public function addClasses(array $classes): self {
        foreach ($classes as $classname => $filename) {
            $this->addClass($classname, $filename);
        }

        return $this;
    }

    public function addClass(string $classname, string $filename): self {
        if (array_key_exists($classname, $this->classes) && $filename !== $this->classes[$classname]) {
            throw new InvalidArgumentException('Die Klasse existiert schon! ' . $classname);
        }

        $this->classes[$classname] = $filename;
        return $this;
    }

    /**
     * @return string
     */
    public function getPath(): string {
        return $this->path;
    }

    public function sortClasses(): self {
        ksort($this->classes);
        return $this;
    }

    public function sortClassesReverse(): self {
        krsort($this->classes);
        return $this;
    }

    public function isEmpty(): bool {
        return 0 === count($this->classes);
    }
}