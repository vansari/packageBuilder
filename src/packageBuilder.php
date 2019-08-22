#!/usr/bin/env php
<?php
declare(strict_types = 1);

use tools\packageBuilder\finder\FileFinder;
use tools\packageBuilder\PackageBuilder;
use tools\packageBuilder\reader\PhpFileReader;
use tools\packageBuilder\writer\PackagesWriter;
use tools\packageBuilder\writer\PackageWriter;
use tools\packageBuilder\writer\WriterOptions;

require __DIR__ . '/../vendor/autoload.php';

function writeUsage(): void {
    echo '++++++ USAGE of packageBuilder.php ++++++' . PHP_EOL;
    echo 'This packageBuilder works completely without the internal core framework yeti.'
        . ' You can use it anywhere you want and where package.php files are required!';
    echo PHP_EOL . PHP_EOL;
    echo '-h | --help                   => Shows this Usage' . PHP_EOL;
    echo '-p | --path                   => The path to the PHP classes (required)' . PHP_EOL;
    echo '-o | --overwrite              => Force overwriting existing package.php classes'
        . ' (optional | default is false)' . PHP_EOL;
    echo '-r | --recursive              => Fetch all files recursive and write a package file for'
        . ' each directory and matching namespace'
        . ' (optional | default is false)' . PHP_EOL;
    echo '-P | --packages-file          => Create the packages.php file in the directory'
        . ' of "--path"' . PHP_EOL;
    echo '-d | --dry-run                => Run the Script, write output to StdOut,'
        . ' do not write files.' . PHP_EOL;
}

$shortOpts = 'p:';
$shortOpts .= 'r';
$shortOpts .= 'o';
$shortOpts .= 'h';
$shortOpts .= 'P';
$shortOpts .= 'd';

$longOpts = [
    'path:', // Pfad zu den Klassen
    'recursive', // Klassen und Namespaces recursive suchen
    'overwrite', // bestehende package Dateien überschreiben
    'help', // Usage anzeigen
    'create-packages-file',
    'dry-run', // Dateien nicht schreiben
];

$options = getopt($shortOpts, $longOpts);
$recursive = false;
$overwrite = false;
$withPackagesFile = false;
$path = null;
$dryrun = false;
foreach ($options as $key => $value) {
    switch ($key) {
        case 'r':
        case 'recursive':
            $recursive = true;
            break;
        case 'o':
        case 'overwrite':
            $overwrite = true;
            break;
        case 'p':
        case 'path':
            $path = $value;
            break;
        case 'P':
        case 'create-packages-file':
            $withPackagesFile = true;
            break;
        case 'd':
        case 'dry-run':
            $dryrun = true;
            break;
        case 'h':
        case 'help':
            writeUsage();
            exit;
    }
}
/** @var null|string $path */
if (null === $path) {
    throw new InvalidArgumentException(
        'Path must be set with "-p" or "--path"'
    );
}

$packageBuilder = PackageBuilder::create($path)
    ->setWriterOptions(new WriterOptions($overwrite, $dryrun, $recursive))
    ->setWithPackagesFile($withPackagesFile)
    ->setIgnoredFilenames(['package.php', 'packages.php']);

$packageBuilder->buildFiles();

/**
 * package Datei loggen
 */
if (!$packageBuilder->getPackageContainer()->isEmpty()) {
    echo str_repeat('*', 50) . PHP_EOL;
    echo 'Generated package.php files: ' . PHP_EOL;
    if ($packageBuilder->getWriterOptions()->isDryRun()) {
        echo str_repeat('*', 50) . PHP_EOL;
        foreach ($packageBuilder->getDryRunResultPackageFiles() as $result) {
            foreach ($result as $path => $content) {
                echo $path . PHP_EOL;
                echo $content . PHP_EOL;
            }
            echo str_repeat('-', 50) . PHP_EOL;
        }
    } else {
        foreach ($packageBuilder->getPackageContainer() as $path) {
            echo $path . PHP_EOL;
        }
    }
    echo str_repeat('*', 50) . PHP_EOL;
}

// packages.php Datei loggen
if ($withPackagesFile) {
    echo PHP_EOL . str_repeat('*', 50) . PHP_EOL;
    echo 'packages.php File generated: ' . PHP_EOL;
    if ($packageBuilder->getWriterOptions()->isDryRun()) {
        echo str_repeat('*', 50) . PHP_EOL;
        foreach ($packageBuilder->getDryRunResultPackagesFile() as $path => $content) {
            echo $path . PHP_EOL;
            echo $content . PHP_EOL;
        }
    } else {
        echo $packageBuilder->getPackagesFilePath() . PHP_EOL;
    }
    echo str_repeat('*', 50) . PHP_EOL;
}