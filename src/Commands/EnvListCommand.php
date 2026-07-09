<?php

declare(strict_types=1);

namespace Tavp\Cli\Commands;

/**
 * tavp env:list — show configured environment adapters.
 */
class EnvListCommand
{
    public function handle(array $args): void
    {
        echo "Configured environment adapters:\n";
        echo "  - lando   (active)\n";
        echo "  - docker  (available)\n";
        echo "  - native  (available)\n";
    }
}
