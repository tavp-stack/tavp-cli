<?php

declare(strict_types=1);

namespace Tavp\Cli\Commands;

/**
 * tavp down — put application into maintenance mode (503).
 */
class DownCommand
{
    public function handle(array $args): void
    {
        $file = getcwd() . '/.tavp-down';
        $message = $args[0] ?? 'We are performing maintenance.';

        $data = [
            'time' => date('c'),
            'message' => $message,
        ];

        file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT));

        echo "Application is now in maintenance mode (503).\n";
        echo "Message: {$message}\n";
        echo "Run `tavp up` to bring it back online.\n";
    }
}
