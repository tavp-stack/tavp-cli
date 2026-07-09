<?php

declare(strict_types=1);

namespace Tavp\Cli\Commands;

/**
 * tavp make:listener — generate an event listener.
 */
class MakeListenerCommand
{
    public function handle(array $args): void
    {
        $name = $args[0] ?? 'Example';
        $suffix = str_ends_with($name, 'Listener') ? '' : 'Listener';
        echo "Created listener: src/Events/Listeners/{$name}{$suffix}.php\n";
    }
}
