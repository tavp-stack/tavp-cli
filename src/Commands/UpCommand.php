<?php

declare(strict_types=1);

namespace Tavp\Cli\Commands;

/**
 * tavp up — bring the application out of maintenance mode.
 */
class UpCommand
{
    public function handle(array $args): void
    {
        if (is_file('.tavp-down')) {
            unlink('.tavp-down');
        }
        echo "Application is back online.\n";
    }
}
