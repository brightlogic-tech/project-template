<?php

declare(strict_types = 1);

namespace App\Service;

final class Preloader
{
    private static int $count = 0;
    /** @var array<string> */
    private array $ignores = [];
    /** @var array<string> */
    private array $paths;

    public function __construct(string ...$paths)
    {
        $this->paths = self::canonicalizePaths($paths);
    }

    public function addPaths(string ...$paths) : self
    {
        $this->paths = \array_merge(
            $this->paths,
            self::canonicalizePaths($paths),
        );

        return $this;
    }

    public function ignore(string ...$paths) : self
    {
        $this->ignores = \array_merge(
            $this->ignores,
            self::canonicalizePaths($paths),
        );

        return $this;
    }

    public function load() : void
    {
        foreach ($this->paths as $path) {
            $this->loadPath($path);
        }

        $count = self::$count;

        echo "[Preloader] Preloaded {$count} classes" . \PHP_EOL;
    }

    private function loadPath(string $path) : void
    {
        if (\is_dir($path)) {
            $this->loadDir($path);

            return;
        }

        $this->loadFile($path);
    }

    private function loadDir(string $path) : void
    {
        $handle = \opendir($path);

        while ($file = \readdir($handle)) {
            if (\in_array($file, ['.', '..'], true)) {
                continue;
            }

            $this->loadPath("{$path}/{$file}");
        }

        \closedir($handle);
    }

    private function loadFile(string $path) : void
    {
        if ($this->shouldIgnore($path)) {
            return;
        }

        @require_once $path;

        self::$count++;
    }

    private function shouldIgnore(string $path) : bool
    {
        if (\pathinfo($path, \PATHINFO_EXTENSION) !== 'php') {
            return true;
        }

        $fileName = \pathinfo($path, \PATHINFO_FILENAME);

        if (\str_contains($path, '/tests/') ||
            \str_contains($path, '/examples/') ||
            \str_contains($path, '/tools/') ||
            \str_ends_with($fileName, 'Test') ||
            \str_ends_with($fileName, 'TestCase') ||
            $fileName === \lcfirst($fileName) ||
            \str_starts_with($fileName, '.')
        ) {
            return true;
        }

        foreach ($this->ignores as $ignore) {
            if (\str_starts_with($path, $ignore)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param array<string> $paths
     * @return array<string>
     */
    private static function canonicalizePaths(array $paths) : array
    {
        $realPath = [];

        foreach ($paths as $name) {
            $temp = \realpath($name);

            if (\is_string($temp)) {
                $realPath[] = $temp;
            }
        }

        return $realPath;
    }
}
