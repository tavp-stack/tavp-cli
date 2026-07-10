<?php

declare(strict_types=1);

namespace Tavp\Cli\Commands;

use Tavp\Cli\Support\GeneratesFiles;

/**
 * tavp make:seeder — generate a database seeder in app/Database/Seeders.
 *
 * Usage: tavp make:seeder <Name>
 */
class MakeSeederCommand
{
    use GeneratesFiles;

    public function handle(array $args): void
    {
        $name = $args[0] ?? null;

        if ($name === null || str_starts_with($name, '--')) {
            echo "Usage: tavp make:seeder <Name>\n";

            return;
        }

        $class = $this->classBase($name, 'Seeder');

        $content = <<<PHP
<?php

declare(strict_types=1);

namespace App\Database\Seeders;

class {$class}
{
    /**
     * Populate the database with seed data.
     */
    public function run(): void
    {
        // Example:
        // \App\Models\User::create(['name' => 'Admin']);
    }
}

PHP;

        $this->put("app/Database/Seeders/{$class}.php", $content);
    }
}
