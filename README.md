# packageBuilder
Package Builder for PHP Classes.

# Usage
++++++ USAGE of packageBuilder.php ++++++\
Tool to collect all PHP classes and generate package.php files for autoloading.\

-h | --help                   => Shows this Usage\
-p | --path                   => The path to the PHP classes (required)\
-o | --overwrite              => Force overwriting existing package.php classes (optional | default is false)\
-r | --recursive              => Fetch all files recursive and write a package file for each directory and matching namespace (optional | default is false)\
-P | --packages-file          => Create the packages.php file in the directory of "--path"\
-d | --dry-run                => Run the Script, write output to StdOut, do not write files.\
