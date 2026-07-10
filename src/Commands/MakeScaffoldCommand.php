<?php

declare(strict_types=1);

namespace Tavp\Cli\Commands;

use Tavp\Cli\Support\GeneratesFiles;

/**
 * tavp make:scaffold — full CRUD generation for a resource.
 *
 * Generates model + migration + resource controller + index/create/edit
 * views, then prints the route line to register.
 *
 * Usage: tavp make:scaffold <Name>
 */
class MakeScaffoldCommand
{
    use GeneratesFiles;

    public function handle(array $args): void
    {
        $name = $args[0] ?? null;

        if ($name === null || str_starts_with($name, '--')) {
            echo "Usage: tavp make:scaffold <Name>\n";

            return;
        }

        $class = $this->studly($name);
        $table = $this->plural($this->snake($class));

        echo "Scaffolding resource '{$class}':\n";

        (new MakeModelCommand())->handle([$class, '--migration', '--resource']);
        (new MakeControllerCommand())->handle([$class, '--resource']);

        foreach (['index', 'create', 'edit'] as $view) {
            (new MakeViewCommand())->handle(["{$table}.{$view}"]);
        }

        echo "\nRegister the route in routes/web.php:\n";
        echo "  \$router->resource('{$table}', \\App\\Controllers\\{$class}Controller::class);\n";
    }
}
