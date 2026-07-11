<?php

declare(strict_types=1);

namespace Tavp\Cli\Commands;

/**
 * tavp remote:add — add a git remote.
 */
class RemoteAddCommand
{
    public function handle(array $args): void
    {
        $name = $args[0] ?? 'origin';
        $url = $args[1] ?? '';

        if ($url === '') {
            echo "Usage: tavp remote:add [name] <url>\n";
            echo "Example: tavp remote:add origin https://github.com/user/repo.git\n";
            return;
        }

        // Check if remote already exists
        exec("git remote get-url {$name} 2>&1", $output, $exitCode);
        if ($exitCode === 0) {
            echo "Remote '{$name}' already exists. Updating URL...\n";
            exec("git remote set-url {$name} {$url}", $output, $exitCode);
            if ($exitCode === 0) {
                echo "Updated remote '{$name}' to {$url}\n";
            } else {
                echo "Error: failed to update remote.\n";
            }
            return;
        }

        // Add remote
        exec("git remote add {$name} {$url}", $output, $exitCode);
        if ($exitCode === 0) {
            echo "Added remote '{$name}' → {$url}\n";
        } else {
            echo "Error: failed to add remote.\n";
        }
    }
}
