<?php

declare(strict_types=1);

namespace Tavp\Cli\Commands;

/**
 * tavp serve — start a development server, Lando-aware.
 */
class ServeCommand
{
    public function handle(array $args): void
    {
        $port = '8000';
        foreach ($args as $arg) {
            if (str_starts_with($arg, '--port=')) {
                $port = substr($arg, strlen('--port='));
            }
        }

        if (getenv('LANDO') !== false || is_file('.lando.yml')) {
            echo "Lando detected. Run inside the container:\n";
            echo "  lando ssh --command=\"php -S 0.0.0.0:{$port} -t public\"\n";

            return;
        }

        echo "Starting PHP dev server on http://localhost:{$port}\n";
        passthru("php -S localhost:{$port} -t public");
    }
}
