<?php

declare(strict_types=1);

namespace Tavp\Cli\Commands;

/**
 * tavp up — bring application out of maintenance mode.
 */
class UpCommand
{
    public function handle(array $args): void
    {
        $file = getcwd() . '/.tavp-down';

        if (is_file($file)) {
            unlink($file);
            echo "Application is back online.\n";
        } else {
            echo "Application is not in maintenance mode.\n";
        }
    }
}
