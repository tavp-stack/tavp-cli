<?php

declare(strict_types=1);

namespace Tavp\Cli\Commands;

use Tavp\Cli\Support\GeneratesFiles;

/**
 * tavp make:module — scaffold a new TAVPkit module under modules/.
 *
 * Usage: tavp make:module <name>
 * Creates module.json, a service provider, and a routes file.
 */
class MakeModuleCommand
{
    use GeneratesFiles;

    public function handle(array $args): void
    {
        $name = $args[0] ?? null;

        if ($name === null || str_starts_with($name, '--')) {
            echo "Usage: tavp make:module <name>\n";

            return;
        }

        $slug = $this->snake($name);
        $studly = $this->studly($name);

        $moduleJson = <<<JSON
{
    "name": "{$slug}",
    "version": "0.1.0",
    "description": "The {$studly} module.",
    "provider": "Modules\\\\{$studly}\\\\{$studly}ServiceProvider"
}

JSON;

        $provider = <<<PHP
<?php

declare(strict_types=1);

namespace Modules\\{$studly};

use Tavp\Core\Module\ServiceProvider;

class {$studly}ServiceProvider implements ServiceProvider
{
    public function register(): void
    {
        // Bind services into the container.
    }

    public function boot(): void
    {
        // Run after all modules are registered.
    }

    public function loadRoutes(): void
    {
        require __DIR__ . '/../routes/web.php';
    }

    public function loadMigrations(): void
    {
        // Register this module's migrations directory.
    }
}

PHP;

        $routes = <<<PHP
<?php

declare(strict_types=1);

// Routes for the {$studly} module.
// Example:
// \$router->get('/{$slug}', [\Modules\\{$studly}\Controllers\\{$studly}Controller::class, 'index']);

PHP;

        echo "Creating module '{$slug}':\n";
        $this->put("modules/{$slug}/module.json", $moduleJson);
        $this->put("modules/{$slug}/src/{$studly}ServiceProvider.php", $provider);
        $this->put("modules/{$slug}/routes/web.php", $routes);
    }
}
