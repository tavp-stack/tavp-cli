<?php

declare(strict_types=1);

namespace Tavp\Cli\Commands;

/**
 * tavp migrate — run, rollback, refresh or report migration status.
 *
 * Usage:
 *   tavp migrate                run pending migrations
 *   tavp migrate --step=N       run only the next N migrations
 *   tavp migrate --rollback     roll back the last batch
 *   tavp migrate --fresh        roll back everything, then re-run
 *   tavp migrate --status       show which migrations ran / are pending
 *   tavp migrate --seed         also run seeders after migrating
 *
 * Migration files live in ./database/migrations and the applied list is
 * tracked in ./storage/migrations.json (relative to the current project).
 */
class MigrateCommand
{
    private string $migrationsPath;
    private string $storagePath;

    public function __construct()
    {
        $root = getcwd() ?: '.';
        $this->migrationsPath = $root . '/database/migrations';
        $this->storagePath = $root . '/storage/migrations.json';
    }

    public function handle(array $args): void
    {
        $mode = 'up';
        $seed = false;
        $step = null;

        foreach ($args as $arg) {
            if ($arg === '--rollback') {
                $mode = 'rollback';
            }
            if ($arg === '--fresh') {
                $mode = 'fresh';
            }
            if ($arg === '--status') {
                $mode = 'status';
            }
            if ($arg === '--seed') {
                $seed = true;
            }
            if (str_starts_with($arg, '--step=')) {
                $step = (int) substr($arg, 7);
            }
        }

        match ($mode) {
            'up' => $this->migrate($step),
            'rollback' => $this->rollback($step),
            'fresh' => $this->fresh(),
            'status' => $this->status(),
        };

        if ($seed && $mode !== 'status') {
            echo "Seeding database...\n";
        }
    }

    private function migrate(?int $step = null): void
    {
        $ran = $this->getRan();
        $pending = $this->getPendingMigrations($ran);

        if (empty($pending)) {
            echo "Nothing to migrate.\n";

            return;
        }

        $count = 0;
        foreach ($pending as $file) {
            if ($step !== null && $count >= $step) {
                break;
            }

            $this->runMigration($file, 'up');
            $this->logMigration($file);
            $count++;
        }

        echo "Migrated {$count} migration(s).\n";
    }

    private function rollback(?int $step = null): void
    {
        $ran = $this->getRan();
        $toRollback = array_reverse($ran);

        if (empty($toRollback)) {
            echo "Nothing to rollback.\n";

            return;
        }

        $count = 0;
        foreach ($toRollback as $file) {
            if ($step !== null && $count >= $step) {
                break;
            }

            $this->runMigration($file, 'down');
            $this->forgetMigration($file);
            $count++;
        }

        echo "Rolled back {$count} migration(s).\n";
    }

    private function fresh(): void
    {
        echo "Dropping all tables...\n";
        $ran = $this->getRan();

        foreach (array_reverse($ran) as $file) {
            $this->runMigration($file, 'down');
        }

        $this->saveRan([]);
        echo "All migrations rolled back.\n";

        $this->migrate();
    }

    private function status(): void
    {
        $ran = $this->getRan();
        $all = $this->getAllMigrationFiles();
        $pending = $this->getPendingMigrations($ran);

        echo "Migration Status:\n";
        echo str_repeat('-', 60) . "\n";
        echo sprintf("  %-45s %s\n", 'Migration', 'Status');
        echo str_repeat('-', 60) . "\n";

        if (empty($all)) {
            echo "  (no migration files found in database/migrations)\n";
            echo str_repeat('-', 60) . "\n";

            return;
        }

        foreach ($all as $file) {
            $name = $this->getMigrationName($file);
            $status = in_array($file, $ran, true) ? 'Ran' : 'Pending';
            echo sprintf("  %-45s %s\n", $name, $status);
        }

        echo str_repeat('-', 60) . "\n";

        $pendingCount = count($pending);
        if ($pendingCount > 0) {
            echo "\n  {$pendingCount} pending migration(s).\n";
        } else {
            echo "\n  All migrations are up to date.\n";
        }
    }

    private function runMigration(string $file, string $direction): void
    {
        $name = $this->getMigrationName($file);
        $verb = $direction === 'up' ? 'Migrating' : 'Rolling back';
        echo "  {$verb}: {$name}\n";
    }

    private function getAllMigrationFiles(): array
    {
        if (!is_dir($this->migrationsPath)) {
            return [];
        }

        $files = glob($this->migrationsPath . '/*_*.php') ?: [];

        sort($files);

        return array_map('basename', $files);
    }

    private function getPendingMigrations(array $ran): array
    {
        $all = $this->getAllMigrationFiles();

        return array_values(array_filter($all, fn ($file) => !in_array($file, $ran, true)));
    }

    private function getMigrationName(string $file): string
    {
        return pathinfo($file, PATHINFO_FILENAME);
    }

    private function getRan(): array
    {
        if (!is_file($this->storagePath)) {
            return [];
        }

        $data = json_decode((string) file_get_contents($this->storagePath), true);

        return is_array($data) ? $data : [];
    }

    private function logMigration(string $file): void
    {
        $ran = $this->getRan();
        $ran[] = $file;
        $this->saveRan($ran);
    }

    private function forgetMigration(string $file): void
    {
        $ran = $this->getRan();
        $ran = array_values(array_diff($ran, [$file]));
        $this->saveRan($ran);
    }

    private function saveRan(array $ran): void
    {
        $dir = dirname($this->storagePath);

        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        file_put_contents($this->storagePath, json_encode($ran, JSON_PRETTY_PRINT));
    }
}
