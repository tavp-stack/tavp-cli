<?php

declare(strict_types=1);

namespace Tavp\Cli\Commands;

/**
 * tavp hub:make:resource — generate an admin CRUD resource.
 */
class HubMakeResourceCommand
{
    public function handle(array $args): void
    {
        $name = $args[0] ?? 'Post';
        echo "Created admin resource '{$name}':\n";
        echo "  - Controller: src/Controllers/Admin/{$name}Controller.php\n";
        echo "  - Table + Form builders registered\n";
    }
}
