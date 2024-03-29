<?php
declare(strict_types = 1);

namespace tools\packageBuilder\writer;

/**
 * Class WriterOptions - Final class with the options how to write the package(s).php
 * @package tools\packageBuilder\writer
 */
final class WriterOptions {

    /** @var bool */
    private $overwriteExisting = false;
    /** @var bool $isDryRun */
    private $isDryRun = false;
    /**
     * @var bool
     */
    private $recursive;
    /**
     * @var bool
     */
    private $withAutogeneratedTs;

    public function __construct(
        bool $overwriteExisting = false,
        bool $isDryRun = false,
        bool $recursive = false,
        bool $withAutogeneratedTs = true
    ) {
        $this->isDryRun = $isDryRun;
        $this->overwriteExisting = $overwriteExisting;
        $this->recursive = $recursive;
        $this->withAutogeneratedTs = $withAutogeneratedTs;
    }

    /**
     * @return bool
     */
    public function isDryRun(): bool {
        return $this->isDryRun;
    }

    /**
     * @return bool
     */
    public function doOverwriteExisting(): bool {
        return $this->overwriteExisting;
    }

    /**
     * @return bool
     */
    public function doRecursive(): bool {
        return $this->recursive;
    }

    /**
     * @return bool
     */
    public function isWithAutogeneratedTs(): bool {
        return $this->withAutogeneratedTs;
    }

    /**
     * @return string
     */
    public function getAutogeneratedTimestampInfoString(): string {
        return '// AUTOGENERATED WITH packageBuilder at '
        . date('Y-m-d H:i:s');
    }
}