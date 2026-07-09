<?php

declare(strict_types=1);

namespace Tavp\Cli\Commands;

/**
 * tavp make:middleware — generate a middleware class.
 */
class MakeMiddlewareCommand
{
    public function handle(array $args): void
    {
        $name = $args[0] ?? 'Example';
        $suffix = str_ends_with($name, 'Middleware') ? '' : 'Middleware';
        echo "Created middleware: src/Http/Middleware/{$name}{$suffix}.php\n";
    }
}
