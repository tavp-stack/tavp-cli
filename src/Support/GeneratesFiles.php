<?php

declare(strict_types=1);

namespace Tavp\Cli\Support;

/**
 * Shared helpers for code-generation commands.
 *
 * All paths resolve against the current working directory (the project
 * root where `tavp` is invoked) — never the vendor/package directory.
 */
trait GeneratesFiles
{
    protected function projectRoot(): string
    {
        return getcwd() ?: '.';
    }

    /**
     * Write a file relative to the project root, creating directories as
     * needed. Skips (without overwriting) if the file already exists.
     *
     * @return bool true when the file was created, false when skipped
     */
    protected function put(string $relativePath, string $contents): bool
    {
        $path = $this->projectRoot() . '/' . ltrim($relativePath, '/');
        $dir = dirname($path);

        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        if (file_exists($path)) {
            echo "  {$relativePath} already exists. Skipping.\n";

            return false;
        }

        file_put_contents($path, $contents);
        echo "  Created {$relativePath}\n";

        return true;
    }

    protected function studly(string $value): string
    {
        return str_replace(' ', '', ucwords(str_replace(['-', '_'], ' ', $value)));
    }

    protected function snake(string $value): string
    {
        $value = str_replace(['-', ' '], '_', $value);

        return strtolower((string) preg_replace('/(?<!^)[A-Z]/', '_$0', $value));
    }

    protected function plural(string $value): string
    {
        $irregular = ['person' => 'people', 'man' => 'men', 'woman' => 'women'];

        if (isset($irregular[$value])) {
            return $irregular[$value];
        }

        if (str_ends_with($value, 'y') && !in_array(substr($value, -2, 1), ['a', 'e', 'i', 'o', 'u'], true)) {
            return substr($value, 0, -1) . 'ies';
        }

        if (str_ends_with($value, 's') || str_ends_with($value, 'x') || str_ends_with($value, 'z')
            || str_ends_with($value, 'ch') || str_ends_with($value, 'sh')) {
            return $value . 'es';
        }

        return $value . 's';
    }

    /**
     * Strip a trailing suffix (e.g. "Controller") if the name already has it,
     * then re-append exactly once. Returns the class base name.
     */
    protected function classBase(string $name, string $suffix): string
    {
        $studly = $this->studly($name);

        if ($suffix !== '' && str_ends_with($studly, $suffix)) {
            return $studly;
        }

        return $studly . $suffix;
    }
}
