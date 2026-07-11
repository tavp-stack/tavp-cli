<?php

declare(strict_types=1);

namespace Tavp\Cli\Commands;

/**
 * tavp cache:clear — clear all cache (volt, file, cms cache).
 */
class CacheClearCommand
{
    public function handle(array $args): void
    {
        $root = getcwd() ?: '.';
        $cleared = 0;

        // 1. Clear compiled Volt templates
        $voltDir = $root . '/storage/compiled/volt';
        if (is_dir($voltDir)) {
            $files = glob($voltDir . '/*.php') ?: [];
            foreach ($files as $file) {
                unlink($file);
            }
            $cleared += count($files);
            echo "  Volt cache: cleared " . count($files) . " files\n";
        }

        // 2. Clear CMS cache
        $cmsCacheDir = $root . '/storage/cms/cache';
        if (is_dir($cmsCacheDir)) {
            $files = glob($cmsCacheDir . '/*.cache') ?: [];
            foreach ($files as $file) {
                unlink($file);
            }
            $cleared += count($files);
            echo "  CMS cache: cleared " . count($files) . " files\n";
        }

        // 3. Clear revisions (optional, only if --revisions flag)
        if (in_array('--revisions', $args, true)) {
            $revDir = $root . '/storage/cms/revisions';
            if (is_dir($revDir)) {
                $files = glob($revDir . '/*/*.json') ?: [];
                foreach ($files as $file) {
                    unlink($file);
                }
                $cleared += count($files);
                echo "  Revisions: cleared " . count($files) . " files\n";
            }
        }

        // 4. Clear OPcache (if available)
        if (function_exists('opcache_reset')) {
            opcache_reset();
            echo "  OPcache: reset\n";
        }

        echo "\nDone! Cleared {$cleared} cache files.\n";
    }
}
