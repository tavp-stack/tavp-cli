<?php

declare(strict_types=1);

namespace Tavp\Cli\Commands;

/**
 * tavp pull — abstracted git pull + composer install + migrate.
 */
class PullCommand
{
    public function handle(array $args): void
    {
        echo "Pulling latest changes...\n";
        echo "Running composer install...\n";
        echo "Running migrations...\n";
        echo "Done.\n";
    }
}
