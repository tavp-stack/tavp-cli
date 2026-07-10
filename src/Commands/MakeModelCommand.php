<?php

declare(strict_types=1);

namespace Tavp\Cli\Commands;

use Tavp\Cli\Support\GeneratesFiles;

/**
 * tavp make:model — generate a model class in app/Models.
 *
 * Usage: tavp make:model <Name> [--migration] [--resource]
 */
class MakeModelCommand
{
    use GeneratesFiles;

    public function handle(array $args): void
    {
        $name = $args[0] ?? null;

        if ($name === null || str_starts_with($name, '--')) {
            echo "Usage: tavp make:model <Name> [--migration] [--resource]\n";

            return;
        }

        $class = $this->studly($name);
        $table = $this->plural($this->snake($class));

        $fillable = in_array('--resource', $args, true)
            ? "'name',\n        // 'email',"
            : "'name',";

        $content = <<<PHP
<?php

declare(strict_types=1);

namespace App\Models;

use Tavp\Core\Database\Model;

class {$class} extends Model
{
    protected string \$table = '{$table}';

    protected array \$fillable = [
        {$fillable}
    ];

    protected array \$casts = [
        // 'metadata' => 'json',
        // 'is_active' => 'boolean',
    ];
}

PHP;

        $this->put("app/Models/{$class}.php", $content);

        if (in_array('--migration', $args, true)) {
            (new MakeMigrationCommand())->handle(["Create{$class}Table", "--table={$table}"]);
        }
    }
}
