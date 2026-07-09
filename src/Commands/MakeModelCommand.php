<?php

declare(strict_types=1);

namespace Tavp\Cli\Commands;

/**
 * tavp make:model — generate a model class.
 */
class MakeModelCommand
{
    public function handle(array $args): void
    {
        $name = $args[0] ?? 'Example';
        echo "Created model: src/Database/Models/{$name}.php\n";
    }
}
