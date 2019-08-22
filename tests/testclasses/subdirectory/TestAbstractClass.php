<?php
declare(strict_types = 1);

namespace tests\packageBuilder\testclasses\subdirectory;

abstract class TestAbstractClass {

    abstract public static function isTestFile(): bool;

    public abstract function myTestFile(): string;

    public static function willRun(): bool {
        return self::isTestFile();
    }
}