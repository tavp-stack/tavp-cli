<?php

declare(strict_types=1);

namespace Tavp\Cli\Commands;

/**
 * tavp down — put the application into maintenance mode.
 */
class DownCommand
{
    public function handle(array $args): void
    {
        file_put_contents('.tavp-down', date('c'));
        echo "Application is now in maintenance mode (503).\n";
    }
}
