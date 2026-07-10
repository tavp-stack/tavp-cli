<?php

declare(strict_types=1);

namespace Tavp\Cli\Commands;

/**
 * tavp migrate:status — show which migrations have run and which are pending.
 *
 * Thin alias over `tavp migrate --status` so the status report has its own
 * discoverable command name.
 */
class MigrateStatusCommand
{
    public function handle(array $args): void
    {
        (new MigrateCommand())->handle(['--status']);
    }
}
