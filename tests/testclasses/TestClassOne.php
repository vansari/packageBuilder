<?php
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

}