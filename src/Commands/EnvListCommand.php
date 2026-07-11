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
        $envFile = getcwd() . '/.env';

        if (!is_file($envFile)) {
            echo "No .env file found.\n";
            return;
        }

        $content = file_get_contents($envFile);
        $lines = explode("\n", $content);

        echo "Environment configuration:\n";
        echo str_repeat('-', 50) . "\n";

        foreach ($lines as $line) {
            $line = trim($line);

            // Skip empty lines and comments
            if ($line === '' || $line[0] === '#') {
                continue;
            }

            // Skip sensitive values
            if (str_contains($line, 'PASSWORD') || str_contains($line, 'SECRET') || str_contains($line, 'KEY')) {
                $parts = explode('=', $line, 2);
                echo "  {$parts[0]}=***\n";
            } else {
                echo "  {$line}\n";
            }
        }

        echo str_repeat('-', 50) . "\n";
    }
}
