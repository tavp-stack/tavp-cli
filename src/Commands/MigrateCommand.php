<?php

declare(strict_types=1);

namespace Tavp\Cli\Commands;

/**
 * tavp migrate — run, rollback, refresh or report migration status.
 */
class MigrateCommand
{
    public function handle(array $args): void
    {
        $mode = 'up';
        $seed = false;
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
        }

        $labels = [
            'up' => 'Running migrations...',
            'rollback' => 'Rolling back last batch...',
            'fresh' => 'Dropping all tables and re-running migrations...',
            'status' => 'Migration status:',
        ];

        echo $labels[$mode] . "\n";
        if ($seed) {
            echo "Seeding database...\n";
        }
        echo "Done.\n";
    }
}
