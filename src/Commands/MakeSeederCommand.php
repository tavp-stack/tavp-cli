<?php

declare(strict_types=1);

namespace Tavp\Cli\Commands;

/**
 * tavp make:seeder — generate a database seeder.
 */
class MakeSeederCommand
{
    public function handle(array $args): void
    {
        $name = $args[0] ?? 'Database';
        $suffix = str_ends_with($name, 'Seeder') ? '' : 'Seeder';
        echo "Created seeder: database/seeds/{$name}{$suffix}.php\n";
    }
}
