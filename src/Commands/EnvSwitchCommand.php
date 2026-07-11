<?php

declare(strict_types=1);

namespace Tavp\Cli\Commands;

/**
 * tavp env:switch — switch the active environment adapter.
 */
class EnvSwitchCommand
{
    public function handle(array $args): void
    {
        $env = $args[0] ?? '';

        if ($env === '') {
            echo "Usage: tavp env:switch <adapter>\n";
            echo "Available: lando, docker, production\n";
            return;
        }

        $valid = ['lando', 'docker', 'production', 'local'];

        if (!in_array($env, $valid, true)) {
            echo "Unknown environment: {$env}\n";
            echo "Available: " . implode(', ', $valid) . "\n";
            return;
        }

        $envFile = getcwd() . '/.env';

        if (!is_file($envFile)) {
            echo "Error: .env file not found.\n";
            return;
        }

        // Update APP_ENV
        $content = file_get_contents($envFile);

        if (str_contains($content, 'APP_ENV=')) {
            $content = preg_replace('/APP_ENV=.*/', "APP_ENV={$env}", $content);
        } else {
            $content = "APP_ENV={$env}\n" . $content;
        }

        file_put_contents($envFile, $content);

        echo "Switched to environment: {$env}\n";

        // Show relevant config
        if ($env === 'lando') {
            echo "  URL: http://localhost:8000\n";
            echo "  DB: MySQL on localhost:3306\n";
        } elseif ($env === 'production') {
            echo "  Make sure to set APP_DEBUG=false in .env\n";
        }
    }
}
