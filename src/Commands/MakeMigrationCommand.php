<?php

declare(strict_types=1);

namespace Tavp\Cli\Commands;

/**
 * tavp make:migration — generate a timestamped migration file.
 *
 * Usage: tavp make:migration <CreateSomething> [--table=name]
 *
 * The file is written to ./database/migrations relative to the current
 * project (getcwd()), never inside the vendor directory.
 */
class MakeMigrationCommand
{
    public function handle(array $args): void
    {
        $name = $args[0] ?? null;

        if ($name === null || str_starts_with($name, '--')) {
            echo "Usage: tavp make:migration <Name> [--table=name]\n";

            return;
        }

        $tableName = $this->extractTable($args) ?? $this->pluralize($this->toSnake($name));
        $fileName = date('Y_m_d_His') . '_' . $this->toSnake($name) . '.php';

        $dir = (getcwd() ?: '.') . '/database/migrations';
        $path = $dir . '/' . $fileName;

        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        if (file_exists($path)) {
            echo "  Migration {$fileName} already exists. Skipping.\n";

            return;
        }

        file_put_contents($path, $this->stub($tableName));
        echo "  Created database/migrations/{$fileName}\n";
    }

    private function stub(string $tableName): string
    {
        return <<<PHP
<?php

declare(strict_types=1);

use Tavp\Core\Database\Migrations\Migration;
use Tavp\Core\Database\Migrations\SchemaBuilder;

return new class extends Migration
{
    public function up(SchemaBuilder \$schema): void
    {
        \$schema->createTable('{$tableName}', function (SchemaBuilder\TableDefinition \$table) {
            \$table->add(\$schema->column('id', 'bigInteger', ['identity' => true, 'primary' => true]));
            \$table->add(\$schema->column('name', 'string'));
            \$table->add(\$schema->column('created_at', 'timestamp'));
            \$table->add(\$schema->column('updated_at', 'timestamp'));
        });
    }

    public function down(SchemaBuilder \$schema): void
    {
        \$schema->dropTable('{$tableName}');
    }
};

PHP;
    }

    private function extractTable(array $args): ?string
    {
        foreach ($args as $arg) {
            if (str_starts_with($arg, '--table=')) {
                return substr($arg, 8);
            }
        }

        return null;
    }

    private function toSnake(string $name): string
    {
        return strtolower((string) preg_replace('/(?<!^)[A-Z]/', '_$0', $name));
    }

    private function pluralize(string $value): string
    {
        $irregular = ['person' => 'people', 'man' => 'men', 'woman' => 'women'];

        if (isset($irregular[$value])) {
            return $irregular[$value];
        }

        if (str_ends_with($value, 'y') && !in_array(substr($value, -2, 1), ['a', 'e', 'i', 'o', 'u'], true)) {
            return substr($value, 0, -1) . 'ies';
        }

        if (str_ends_with($value, 's') || str_ends_with($value, 'x') || str_ends_with($value, 'z')
            || str_ends_with($value, 'ch') || str_ends_with($value, 'sh')) {
            return $value . 'es';
        }

        return $value . 's';
    }
}
