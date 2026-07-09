<?php

declare(strict_types=1);

namespace Tavp\Cli\Commands;

/**
 * tavp kit:install — install a TAVPkit bundle (teams, api, profile, 2fa).
 */
class KitInstallCommand
{
    public function handle(array $args): void
    {
        $kit = $args[0] ?? 'teams';
        echo "Installing TAVPkit '{$kit}'...\n";
        echo "  - Migrations, controllers, views added\n";
        echo "Run: tavp migrate && tavp key:generate\n";
    }
}
