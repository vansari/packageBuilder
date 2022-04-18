# packageBuilder
Package Builder for PHP Classes.

# Usage
++++++ USAGE of packageBuilder.php ++++++\
Tool to collect all PHP classes and generate package.php files for autoloading.

-h | --help                   => Shows this Usage\
-p | --path                   => The path to the PHP classes (required)\
-o | --overwrite              => Force overwriting existing package.php classes (optional | default is false)\
-r | --recursive              => Fetch all files recursive and write a package file for each directory and matching namespace (optional | default is false)\
-P | --packages-file          => Create the packages.php file in the directory of "--path"\
-d | --dry-run                => Run the Script, write output to StdOut, do not write files.

# Tests

Install dev-dependencies with korrekt PHP_VERSION:
```shell
export PHP_VERSION="8.1.4"

docker build --build-arg PHP_VERSION="${PHP_VERSION}" --no-cache -t composer:local --target composer-php -f docker/Dockerfile .
docker run --rm -v ${PWD}:/code -w /code composer:local composer install
```
Run tests under your PHP Version
```shell
export PHP_VERSION="8.1.4"
docker run --rm -v ${PWD}:/code -w /code php:"${PHP_VERSION}"-cli-alpine vendor/bin/phpunit
```