<?php
declare(strict_types = 1);

namespace tests\packageBuilder\testclasses;

use \InvalidArgumentException;
use tests\packageBuilder\testclasses\subdirectory\TestClassTwo;

class TestClassFactory {

    public static function build(string $name): TestClassInterface {
        switch ($name) {
            case 'foo':
                return new TestClassOne($name);
            case 'bar':
                return new TestClassTwo(100);
            case TestClassOne::class:
                return new TestClassOne('bar');
            default:
                throw new InvalidArgumentException();
        }
    }
}