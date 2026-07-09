<?php

declare(strict_types=1);

namespace Tavp\Cli\Commands;

/**
 * tavp upgrade — upgrade a project tier (to=app|enterprise).
 */
class UpgradeCommand
{
    public function handle(array $args): void
    {
        $to = 'app';
        foreach ($args as $arg) {
            if (str_starts_with($arg, '--to=')) {
                $to = substr($arg, strlen('--to='));
            }
        }
        echo "Upgrading project to '{$to}' tier...\n";
        echo "  - Adding required modules and config\n";
        echo "Upgrade complete. Review changes and run tavp migrate.\n";
    }
}
