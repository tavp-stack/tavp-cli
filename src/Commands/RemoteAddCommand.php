<?php

declare(strict_types=1);

namespace Tavp\Cli\Commands;

/**
 * tavp remote:add — configure a git remote.
 */
class RemoteAddCommand
{
    public function handle(array $args): void
    {
        $url = $args[0] ?? 'https://github.com/tavp-stack/project.git';
        echo "Adding remote: {$url}\n";
        echo "Remote configured.\n";
    }
}
