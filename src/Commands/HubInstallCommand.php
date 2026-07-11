<?php

declare(strict_types=1);

namespace Tavp\Cli\Commands;

/**
 * tavp hub:install — install TAVPhub admin panel into project.
 */
class HubInstallCommand
{
    public function handle(array $args): void
    {
        $dir = getcwd();

        echo "Installing TAVPhub admin panel...\n";

        // Check if already installed
        if (is_dir($dir . '/vendor/tavp/tavphub')) {
            echo "TAVPhub is already installed.\n";
            return;
        }

        // Run composer require
        echo "Adding tavp/tavphub dependency...\n";
        exec('composer require tavp/tavphub 2>&1', $output, $exitCode);
        foreach ($output as $line) {
            echo "  {$line}\n";
        }

        if ($exitCode !== 0) {
            echo "Error: composer require failed.\n";
            return;
        }

        // Create hub config
        $configDir = $dir . '/config';
        if (!is_dir($configDir)) {
            mkdir($configDir, 0755, true);
        }

        $configFile = $configDir . '/hub.php';
        if (!is_file($configFile)) {
            $config = <<<'CONFIG'
<?php

return [
    'admin_prefix' => '/admin',
    'brand' => env('APP_NAME', 'My App'),
    'resources' => [],
];
CONFIG;
            file_put_contents($configFile, $config);
            echo "Created config/hub.php\n";
        }

        echo "TAVPhub installed!\n";
        echo "\nNext steps:\n";
        echo "  1. Add resources to config/hub.php\n";
        echo "  2. Add routes: \\Tavp\\Hub\\HubModule::routes($router);\n";
        echo "  3. Visit /admin\n";
    }
}
