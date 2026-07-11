<?php

declare(strict_types=1);

namespace Tavp\Cli\Commands;

/**
 * tavp kit:install — install a TAVPkit starter kit.
 */
class KitInstallCommand
{
    private array $kits = [
        'blog' => [
            'description' => 'Blog with posts, categories, and tags',
            'modules' => ['tavp-cms'],
            'content_types' => ['post', 'page'],
        ],
        'saas' => [
            'description' => 'SaaS starter with auth, billing, and teams',
            'modules' => ['tavp-cms', 'tavpid', 'tavphive'],
            'content_types' => ['page'],
        ],
        'ecommerce' => [
            'description' => 'E-commerce with products and orders',
            'modules' => ['tavp-cms'],
            'content_types' => ['product', 'order', 'page'],
        ],
    ];

    public function handle(array $args): void
    {
        $kit = $args[0] ?? '';

        if ($kit === '') {
            echo "Usage: tavp kit:install <kit>\n";
            echo "Available kits:\n";
            foreach ($this->kits as $name => $config) {
                echo "  {$name} — {$config['description']}\n";
            }
            return;
        }

        if (!isset($this->kits[$kit])) {
            echo "Unknown kit: {$kit}\n";
            echo "Available kits: " . implode(', ', array_keys($this->kits)) . "\n";
            return;
        }

        $config = $this->kits[$kit];
        $dir = getcwd();

        echo "Installing kit: {$kit}\n";
        echo "Description: {$config['description']}\n\n";

        // Install modules
        foreach ($config['modules'] as $module) {
            echo "Installing {$module}...\n";
            exec("composer require tavp/{$module} 2>&1", $output, $exitCode);
            if ($exitCode !== 0) {
                echo "  Warning: composer require {$module} failed\n";
            }
        }

        // Create content types in config
        $cmsConfigFile = $dir . '/config/cms.php';
        if (is_file($cmsConfigFile)) {
            $cmsConfig = include $cmsConfigFile;

            foreach ($config['content_types'] as $type) {
                if (!isset($cmsConfig['content_types'][$type])) {
                    echo "  Content type '{$type}' needs to be configured in config/cms.php\n";
                }
            }
        }

        echo "\nKit '{$kit}' installed!\n";
        echo "\nNext steps:\n";
        echo "  1. Configure content types in config/cms.php\n";
        echo "  2. Run `tavp migrate`\n";
        echo "  3. Run `tavp serve`\n";
    }
}
