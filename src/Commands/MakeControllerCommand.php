<?php

declare(strict_types=1);

namespace Tavp\Cli\Commands;

/**
 * tavp make:controller — generate a controller (web/api/resource).
 */
class MakeControllerCommand
{
    public function handle(array $args): void
    {
        $name = $args[0] ?? 'Example';
        $type = 'web';
        foreach ($args as $arg) {
            if (in_array($arg, ['--api', '--resource'], true)) {
                $type = ltrim($arg, '--');
            }
        }
        $suffix = str_ends_with($name, 'Controller') ? '' : 'Controller';
        echo "Created {$type} controller: src/Controllers/{$name}{$suffix}.php\n";
    }
}
