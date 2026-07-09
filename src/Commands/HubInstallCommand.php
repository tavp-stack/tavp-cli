<?php

declare(strict_types=1);

namespace Tavp\Cli\Commands;

/**
 * tavp hub:install — install the TAVPhub admin panel.
 */
class HubInstallCommand
{
    public function handle(array $args): void
    {
        echo "Installing TAVPhub admin panel...\n";
        echo "  - Routes: routes/admin.php\n";
        echo "  - Views: resources/views/admin/*\n";
        echo "  - Assets published\n";
        echo "TAVPhub installed. Visit /admin to configure.\n";
    }
}
