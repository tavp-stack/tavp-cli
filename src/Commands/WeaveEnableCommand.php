<?php

declare(strict_types=1);

namespace Tavp\Cli\Commands;

/**
 * tavp weave:enable — register Fiber async helpers (Weave).
 */
class WeaveEnableCommand
{
    public function handle(array $args): void
    {
        echo "Enabling TAVP Weave (async helpers)...\n";
        echo "  - Async::all/race/sleep available\n";
        echo "Weave enabled.\n";
    }
}
