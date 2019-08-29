<?php
declare(strict_types = 1);

namespace tools\packageBuilder\reader;

use InvalidArgumentException;
use RuntimeException;

class PhpFileReader {

    /**
     * Holt alle PHP Tokens aus dem übergebenen $content
     * @param string $content
     *
     * @return array
     */
    public function getTokens(string $file): array {
        $this->claimFile($file);
        if (!extension_loaded('tokenizer')) {
            throw new RuntimeException('Extension "tokenizer" was not loaded');
        }

        return token_get_all($this->readContent($file));
    }

    /**
     * Liest den Content aus dem $file
     * @param string $file
     *
     * @return string
     */
    public function readContent(string $file): string {
        $this->claimFile($file);

        return file_get_contents($file);
    }

    /**
     * Iteriert über die $tokens des $files
     * und gibt den $namespace sowie $classname und $filename zurück
     * @param string $file
     * @param null|array $tokens
     *
     * @return array
     */
    public function fetchClass(string $file, ?array $tokens = null): array {
        $this->claimFile($file);
        $relativeFileName = pathinfo(realpath($file), PATHINFO_BASENAME);
        $classExpected = false;
        $namespaceExpected = false;
        $namespace = '';
        $classes = [];
        $loopTokens = $tokens ?? $this->getTokens($file);
        foreach ($loopTokens as $index => $token) {
            if (is_string($token)){
                if (';' === $token && $namespaceExpected) {
                    $namespaceExpected = false;
                    continue;
                }
                continue;
            }
            list($id, $content) = $token;
            switch($id) {
                case T_CLASS:
                    // ::class muss ignoriert werden!
                    if (T_DOUBLE_COLON === $loopTokens[--$index][0]) {
                        break;
                    }
                case T_ABSTRACT:
                    $nextTokenAbstractFunction = $loopTokens[$index + 2][0];
                    if (
                        T_PUBLIC === $nextTokenAbstractFunction
                        || T_PROTECTED === $nextTokenAbstractFunction
                        || T_PRIVATE === $nextTokenAbstractFunction
                        || T_FUNCTION === $nextTokenAbstractFunction
                    ) {
                        break;
                    }
                case T_INTERFACE:
                case T_TRAIT:
                    $classExpected = true;
                    break;
                case T_NAMESPACE:
                    $namespaceExpected = true;
                    break;
                case ($id == T_STRING && $classExpected):
                    $classes[$content] = $relativeFileName;
                    $classExpected = false;
                    break;
                case ($id == T_STRING && $namespaceExpected):
                    $namespace .= $content;
                    break;
                case ($id == T_NS_SEPARATOR && $namespaceExpected):
                    $namespace .= $content;
                    break;
                case ($content === "\n\n" && $namespaceExpected):
                    $namespaceExpected = false;
                    break;
            }
        }

        return [$namespace, $classes];
    }

    /**
     * Prüft, ob das $file ein nicht leerer String ist und als Datei existiert
     * @param string $file
     */
    private function claimFile(string $file): void {
        if ('' !== $file && is_file($file)) {
            return;
        }
        throw new InvalidArgumentException('Filepath is invalid!');
    }
}