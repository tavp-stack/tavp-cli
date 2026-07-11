<?php

declare(strict_types=1);

namespace Tavp\Cli\Commands;

/**
 * tavp weave:enable — enable TAVP Weave (async helpers).
 */
class WeaveEnableCommand
{
    public function handle(array $args): void
    {
        $dir = getcwd();

        echo "Enabling TAVP Weave (async helpers)...\n\n";

        // Check if already enabled
        $configFile = $dir . '/config/app.php';
        if (is_file($configFile)) {
            $content = file_get_contents($configFile);
            if (str_contains($content, "'enabled' => true") && str_contains($content, 'weave')) {
                echo "TAVP Weave is already enabled.\n";
                return;
            }
        }

        // Create/update config
        if (!is_dir($dir . '/config')) {
            mkdir($dir . '/config', 0755, true);
        }

        $configFile = $dir . '/config/weave.php';
        $config = <<<'CONFIG'
<?php

return [
    'enabled' => true,
    'timeout' => 30,
    'max_concurrent' => 10,
];
CONFIG;

        file_put_contents($configFile, $config);
        echo "Created config/weave.php\n";

        // Add to .env if not exists
        $envFile = $dir . '/.env';
        if (is_file($envFile)) {
            $content = file_get_contents($envFile);
            if (!str_contains($content, 'WEAVE_ENABLED=')) {
                $content .= "\nWEAVE_ENABLED=true\n";
                file_put_contents($envFile, $content);
                echo "Added WEAVE_ENABLED=true to .env\n";
            }
        }

        echo "\nTAVP Weave enabled!\n";
        echo "\nUsage in code:\n";
        echo "  // Async HTTP request\n";
        echo '  $response = async(fn () => file_get_contents("https://api.example.com"));' . "\n";
        echo "\n  // Parallel execution\n";
        echo '  $results = parallel([' . "\n";
        echo '      fn () => fetchDataFromApi1(),' . "\n";
        echo '      fn () => fetchDataFromApi2(),' . "\n";
        echo '  ]);' . "\n";
    }
}
