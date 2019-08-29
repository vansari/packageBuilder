<?php
declare(strict_types = 1);

namespace tests\packageBuilder\testclasses;

abstract class TestAbstractClass {

    abstract public static function isTestFile(): bool;

    public abstract function myTestFile(): string;

    abstract protected static function isNotTestFile(): bool;

    protected abstract function myTestFileName(): string;

    public static function willRun(): bool {
        return self::isTestFile();
    }
}