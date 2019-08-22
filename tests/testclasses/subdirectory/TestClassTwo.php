<?php
declare(strict_types = 1);

namespace tests\packageBuilder\testclasses\subdirectory;

use tests\packageBuilder\testclasses\TestClassInterface;

class TestClassTwo implements TestClassInterface {

    private $property = 0;

    public function __construct(int $property) {
        $this->property = $property;
    }

    /**
     * @return int
     */
    public function getProperty(): int {
        return $this->property;
    }

    /**
     * @param int $property
     *
     * @return $this
     */
    public function setProperty(int $property): self {
        $this->property = $property;

        return $this;
    }
}