<?php

declare(strict_types=1);

namespace Tavp\Cli\Commands;

/**
 * tavp pull — pull latest changes from remote.
 */
class PullCommand
{
    public function handle(array $args): void
    {
        $branch = $args[0] ?? 'main';

        echo "Pulling latest changes from {$branch}...\n";

        exec("git pull origin {$branch} 2>&1", $output, $exitCode);

        foreach ($output as $line) {
            echo "  {$line}\n";
        }

        if ($exitCode !== 0) {
            echo "Error: git pull failed. Check your remote configuration.\n";
            return;
        }

        // Check if composer.lock changed
        exec('git diff --name-only HEAD@{1} HEAD', $diffOutput);
        if (in_array('composer.lock', $diffOutput)) {
            echo "composer.lock changed, running composer install...\n";
            exec('composer install --no-dev --optimize-autoloader', $output, $exitCode);
            foreach ($output as $line) {
                echo "  {$line}\n";
            }
        }

        echo "Done!\n";
    }
}
