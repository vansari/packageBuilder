<?php
declare(strict_types = 1);

namespace tools\packageBuilder\sorter;

use tools\packageBuilder\reader\PhpFileReader;
use tools\packageBuilder\util\ClassHolder;
use tools\packageBuilder\util\ClassHolderContainer;

/**
 * Class ClassFileSorter
 * @package tools\packageBuilder\sorter
 */
class ClassFileSorter {

    /**
     * @param array $files
     * @return ClassHolderContainer
     */
    public static function getSortedClassHolderContainer(array $files): ClassHolderContainer {
        $container = new ClassHolderContainer();
        foreach ($files as $file) {
            $filepath = dirname($file);
            list($namespace, $classDefinitions) = (new PhpFileReader())->fetchClass($file);
            if (empty($classDefinitions)) {
                continue;
            }
            if (null === $container->getClassHolderByNamespace($namespace)) {
                $classHolder = new ClassHolder($filepath);
                $classHolder->setNamespace($namespace);
                $container->addClassHolder($classHolder);
            }
            $container
                ->getClassHolderByNamespace($namespace)
                ->addClasses(self::getClasses($classDefinitions))
                ->sortClasses();
        }

        return $container;
    }

    /**
     * @param ClassHolder $classHolder
     * @param array $classDefinitions
     * @return ClassHolder
     */
    private static function getClasses(array $classDefinitions): array {
        $classes = [];
        foreach($classDefinitions as $class => $filename) {
            $classes[$class] = $filename;
        }

        return $classes;
    }
}