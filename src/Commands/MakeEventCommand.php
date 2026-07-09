<?php

declare(strict_types=1);

namespace Tavp\Cli\Commands;

/**
 * tavp make:event — generate an event class.
 */
class MakeEventCommand
{
    public function handle(array $args): void
    {
        $name = $args[0] ?? 'Example';
        $suffix = str_ends_with($name, 'Event') ? '' : 'Event';
        echo "Created event: src/Events/{$name}{$suffix}.php\n";
    }
}
