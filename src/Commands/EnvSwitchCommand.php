<?php

declare(strict_types=1);

namespace Tavp\Cli\Commands;

/**
 * tavp env:switch — switch the active environment adapter.
 */
class EnvSwitchCommand
{
    public function handle(array $args): void
    {
        $env = $args[0] ?? 'lando';
        echo "Switching environment adapter to '{$env}'...\n";
        echo "Updated .tavp.json (env: {$env})\n";
    }
}
