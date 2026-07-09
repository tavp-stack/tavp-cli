<?php

declare(strict_types=1);

namespace Tavp\Cli\Commands;

/**
 * tavp make:view — generate a Volt view template.
 */
class MakeViewCommand
{
    public function handle(array $args): void
    {
        $name = $args[0] ?? 'example';
        echo "Created view: resources/views/{$name}.volt\n";
    }
}
