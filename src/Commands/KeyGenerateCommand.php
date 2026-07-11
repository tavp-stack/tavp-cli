<?php

declare(strict_types=1);

namespace Tavp\Cli\Commands;

/**
 * tavp key:generate — generate APP_KEY and JWT_SECRET, write to .env.
 */
class KeyGenerateCommand
{
    public function handle(array $args): void
    {
        $envFile = getcwd() . '/.env';

        if (!is_file($envFile)) {
            echo "Error: .env file not found. Run `cp .env.example .env` first.\n";
            exit(1);
        }

        $appKey = 'base64:' . base64_encode(random_bytes(32));
        $jwtSecret = bin2hex(random_bytes(32));

        $content = file_get_contents($envFile);

        // Replace or append APP_KEY
        if (str_contains($content, 'APP_KEY=')) {
            $content = preg_replace('/APP_KEY=.*/', "APP_KEY={$appKey}", $content);
        } else {
            $content .= "\nAPP_KEY={$appKey}\n";
        }

        // Replace or append JWT_SECRET
        if (str_contains($content, 'JWT_SECRET=')) {
            $content = preg_replace('/JWT_SECRET=.*/', "JWT_SECRET={$jwtSecret}", $content);
        } else {
            $content .= "JWT_SECRET={$jwtSecret}\n";
        }

        file_put_contents($envFile, $content);

        echo "APP_KEY={$appKey}\n";
        echo "JWT_SECRET={$jwtSecret}\n";
        echo "Written to .env\n";
    }
}
