<?php

declare(strict_types=1);

namespace Tavp\Cli\Commands;

use Tavp\Cli\Support\GeneratesFiles;

/**
 * tavp hub:install — install TAVPhub admin panel into a TAVP project.
 */
class HubInstallCommand
{
    use GeneratesFiles;

    public function handle(array $args): void
    {
        $root = $this->projectRoot();

        echo "Installing TAVPhub admin panel...\n";

        if (is_dir($root . '/vendor/tavp/tavphub')) {
            echo "TAVPhub is already installed.\n";
        } else {
            echo "Adding tavp/tavphub dependency...\n";
            exec('composer require tavp/tavphub 2>&1', $output, $exitCode);
            foreach ($output as $line) {
                echo "  {$line}\n";
            }
            if ($exitCode !== 0) {
                echo "Error: composer require failed.\n";
                return;
            }
        }

        echo "Suggesting tavp/tavpblocks for ready-made UI components...\n";
        exec('composer require tavp/tavpblocks --dev 2>&1', $output, $exitCode);
        foreach ($output as $line) {
            echo "  {$line}\n";
        }

        $this->createConfig($root);
        $this->registerRoutes($root);

        echo "\nTAVPhub installed!\n";
        echo "  Visit /admin after building your resources with:\n";
        echo "    tavp hub:make:resource Product\n";
    }

    private function createConfig(string $root): void
    {
        $configFile = $root . '/config/hub.php';
        if (is_file($configFile)) {
            echo "config/hub.php already exists. Skipping.\n";
            return;
        }

        if (!is_dir($root . '/config')) {
            mkdir($root . '/config', 0755, true);
        }

        $config = <<<'CONFIG'
<?php

return [
    'admin_prefix' => '/admin',
    'brand' => env('APP_NAME', 'TAVP'),
    'auth' => [
        'guard' => \Tavp\Hub\Auth\SessionGuard::class,
        'otp_issuer' => 'tavpid',
    ],
    'resources' => [
        // Explicit resources: 'product' => \App\Resources\ProductResource::class,
    ],
    'discovery' => [
        'enabled' => true,
        'path' => __DIR__ . '/../app/Resources',
        'namespace' => 'App\Resources',
    ],
];

CONFIG;

        file_put_contents($configFile, $config);
        echo "Created config/hub.php (auto-discovery enabled)\n";
    }

    /**
     * Register routes by appending to routes/web.php (TAVP convention).
     * Creates the file with a minimal router bootstrap if it is missing.
     */
    private function registerRoutes(string $root): void
    {
        $routesFile = $root . '/routes/web.php';

        if (is_file($routesFile) && str_contains(file_get_contents($routesFile), 'HubController::routes')) {
            echo "Routes already registered in routes/web.php. Skipping.\n";
            return;
        }

        $snippet = "\n// TAVPhub admin panel\n"
            . "\\Tavp\\Hub\\HubController::routes(\$router);\n";

        if (is_file($routesFile)) {
            $content = rtrim(file_get_contents($routesFile), "\n") . $snippet . "\n";
            file_put_contents($routesFile, $content);
            echo "Registered routes in routes/web.php\n";
            return;
        }

        if (!is_dir($root . '/routes')) {
            mkdir($root . '/routes', 0755, true);
        }

        $bootstrap = <<<'PHP'
<?php

// TAVP routes file. Provide a Phalcon Router instance named $router,
// then register TAVPhub.
use Phalcon\Mvc\Router;
use Tavp\Hub\HubController;

$router = new Router();
$router->removeExtraSlashes(true);

HubController::routes($router);

return $router;

PHP;

        file_put_contents($routesFile, $bootstrap);
        echo "Created routes/web.php with TAVPhub routes\n";
    }
}
