<?php

declare(strict_types=1);

namespace Tavp\Cli\Commands;

/**
 * tavp env:add — add an additional environment adapter.
 */
class EnvAddCommand
{
    public function handle(array $args): void
    {
        $env = $args[0] ?? 'docker';
        echo "Adding environment adapter '{$env}'...\n";
        echo "Generated adapter config for {$env}.\n";
    }
}
