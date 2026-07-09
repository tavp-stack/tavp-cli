<?php

declare(strict_types=1);

namespace Tavp\Cli\Commands;

/**
 * tavp key:generate — generate APP_KEY and JWT_SECRET.
 */
class KeyGenerateCommand
{
    public function handle(array $args): void
    {
        $appKey = 'base64:' . base64_encode(random_bytes(32));
        $jwtSecret = bin2hex(random_bytes(32));
        echo "APP_KEY={$appKey}\n";
        echo "JWT_SECRET={$jwtSecret}\n";
        echo "Add these to your .env file.\n";
    }
}
