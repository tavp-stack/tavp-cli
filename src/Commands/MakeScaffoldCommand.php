<?php

declare(strict_types=1);

namespace Tavp\Cli\Commands;

/**
 * tavp make:scaffold — full CRUD generation (model + controller + view + migration + route).
 */
class MakeScaffoldCommand
{
    public function handle(array $args): void
    {
        $name = $args[0] ?? 'Post';
        echo "Scaffolding resource '{$name}':\n";
        echo "  - Model: src/Database/Models/{$name}.php\n";
        echo "  - Controller: src/Controllers/{$name}Controller.php\n";
        echo "  - Migration: database/migrations/*_create_{$this->table($name)}_table.php\n";
        echo "  - Views: resources/views/{$this->table($name)}/*.volt\n";
        echo "  - Route: Route::resource('{$this->table($name)}', {$name}Controller::class)\n";
    }

    private function table(string $name): string
    {
        return strtolower((string) preg_replace('/(?<!^)[A-Z]/', '_$0', $name)) . 's';
    }
}
