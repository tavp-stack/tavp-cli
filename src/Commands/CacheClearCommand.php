<?php

declare(strict_types=1);

namespace Tavp\Cli\Commands;

use Tavp\Core\Application;

/**
 * tavp cache:clear — clear all application cache.
 *
 * Removes compiled Volt templates, file cache, and CMS cache.
 */
class CacheClearCommand
{
    public function handle(array $args): void
    {
        $this->info('Clearing application cache...');

        try {
            $base = Application::getInstance()->getBasePath();
        } catch (\Throwable) {
            $base = dirname(__DIR__, 5);
        }

        $dirs = [
            'Compiled Volt templates' => $base . '/storage/compiled/volt',
            'File cache' => $base . '/storage/cache',
            'CMS cache' => $base . '/storage/cms/cache',
        ];

        $totalCleared = 0;

        foreach ($dirs as $label => $dir) {
            if (!is_dir($dir)) {
                $this->comment("  {$label}: skipped (not found)");
                continue;
            }

            $count = $this->clearDir($dir);
            $totalCleared += $count;
            $this->line("  <info>{$label}</info>: cleared <comment>{$count}</comment> entries");
        }

        echo "\n";
        $this->success("Cache cleared! Removed {$totalCleared} entries.");
    }

    private function clearDir(string $dir): int
    {
        $count = 0;
        $files = glob($dir . '/*');

        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
                $count++;
            } elseif (is_dir($file)) {
                $count += $this->countDir($file);
                $this->removeDir($file);
            }
        }

        return $count;
    }

    private function countDir(string $dir): int
    {
        $count = 0;
        $files = array_diff(scandir($dir), ['.', '..']);

        foreach ($files as $file) {
            $path = $dir . '/' . $file;
            if (is_file($path)) {
                $count++;
            } elseif (is_dir($path)) {
                $count += $this->countDir($path);
            }
        }

        return $count;
    }

    private function removeDir(string $dir): void
    {
        $files = array_diff(scandir($dir), ['.', '..']);
        foreach ($files as $file) {
            $path = $dir . '/' . $file;
            is_dir($path) ? $this->removeDir($path) : unlink($path);
        }
        rmdir($dir);
    }

    private function info(string $msg): void
    {
        echo "\033[36m{$msg}\033[0m\n";
    }

    private function comment(string $msg): void
    {
        echo "\033[33m{$msg}\033[0m\n";
    }

    private function line(string $msg): void
    {
        $clean = preg_replace('/<[^>]+>/', '', $msg);
        echo "  {$clean}\n";
    }

    private function success(string $msg): void
    {
        echo "\033[32m✔ {$msg}\033[0m\n";
    }
}
