<?php

declare(strict_types=1);

namespace Tavp\Cli\Commands;

/**
 * tavp make:module — scaffold a new TAVPkit module with module.json.
 */
class MakeModuleCommand
{
    public function handle(array $args): void
    {
        $name = $args[0] ?? 'example';
        echo "Created module '{$name}':\n";
        echo "  - modules/{$name}/module.json\n";
        echo "  - modules/{$name}/src/ModuleServiceProvider.php\n";
        echo "  - modules/{$name}/routes/web.php\n";
    }
}
