<?php

declare(strict_types=1);

namespace Tavp\Cli\Commands;

/**
 * tavp migrate:status — show migration status.
 */
class MigrateStatusCommand
{
    public function handle(array $args): void
    {
        $dir = getcwd();
        $migrationsDir = $dir . '/database/migrations';

        if (!is_dir($migrationsDir)) {
            echo "No migrations directory found.\n";
            return;
        }

        // Get all migration files
        $files = glob($migrationsDir . '/*.php');
        sort($files);

        // Get executed migrations from storage
        $statusFile = $dir . '/storage/migrations.json';
        $executed = [];
        if (is_file($statusFile)) {
            $executed = json_decode(file_get_contents($statusFile), true) ?? [];
        }

        echo "Migration Status\n";
        echo str_repeat('-', 60) . "\n";
        echo sprintf("%-40s %-10s\n", "Migration", "Status");
        echo str_repeat('-', 60) . "\n";

        $pending = 0;
        $completed = 0;

        foreach ($files as $file) {
            $name = basename($file);
            $isExecuted = in_array($name, $executed, true);

            if ($isExecuted) {
                echo sprintf("%-40s ✓ Done\n", $name);
                $completed++;
            } else {
                echo sprintf("%-40s ○ Pending\n", $name);
                $pending++;
            }
        }

        echo str_repeat('-', 60) . "\n";
        echo "Completed: {$completed} | Pending: {$pending}\n";

        if ($pending > 0) {
            echo "\nRun `tavp migrate` to execute pending migrations.\n";
        }
    }
}
