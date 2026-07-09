<?php

declare(strict_types=1);

namespace Tavp\Cli\Commands;

/**
 * tavp make:migration — generate a timestamped migration file.
 */
class MakeMigrationCommand
{
    public function handle(array $args): void
    {
        $name = $args[0] ?? 'CreateTable';
        $fileName = date('Y_m_d_His') . '_' . $this->toSnake($name) . '.php';
        echo "Created migration: database/migrations/{$fileName}\n";
    }

    private function toSnake(string $name): string
    {
        return strtolower((string) preg_replace('/(?<!^)[A-Z]/', '_$0', $name));
    }
}
