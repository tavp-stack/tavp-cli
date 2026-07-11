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
        try {
            $base = Application::getInstance()->getBasePath();
        } catch (\Throwable) {
            $base = dirname(__DIR__, 5);
        }

        $dirs = [
            $base . '/storage/compiled/volt',
            $base . '/storage/cache',
            $base . '/storage/cms/cache',
        ];

        $cleared = 0;

        foreach ($dirs as $dir) {
            if (!is_dir($dir)) {
                continue;
            }

            $files = glob($dir . '/*');
            foreach ($files as $file) {
                if (is_file($file)) {
                    unlink($file);
                    $cleared++;
                } elseif (is_dir($file)) {
                    $this->removeDir($file);
                    $cleared++;
                }
            }
        }

        echo "Cleared {$cleared} cache entries.\n";
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
}
