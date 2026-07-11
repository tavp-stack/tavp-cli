<?php

declare(strict_types=1);

namespace Tavp\Cli\Commands;

/**
 * tavp upgrade — upgrade project to a higher tier.
 */
class UpgradeCommand
{
    private array $tiers = [
        'basic' => [
            'description' => 'Basic: core + CMS',
            'packages' => ['tavp/core', 'tavp/tavp-cms'],
        ],
        'pro' => [
            'description' => 'Pro: core + CMS + auth + admin',
            'packages' => ['tavp/core', 'tavp/tavp-cms', 'tavp/tavpid', 'tavp/tavphub'],
        ],
        'enterprise' => [
            'description' => 'Enterprise: all modules',
            'packages' => ['tavp/core', 'tavp/tavp-cms', 'tavp/tavpid', 'tavp/tavphub', 'tavp/tavphive', 'tavp/tavpkit'],
        ],
    ];

    public function handle(array $args): void
    {
        $tier = $args[0] ?? '';

        if ($tier === '') {
            echo "Usage: tavp upgrade <tier>\n";
            echo "Available tiers:\n";
            foreach ($this->tiers as $name => $config) {
                echo "  {$name} — {$config['description']}\n";
            }
            return;
        }

        if (!isset($this->tiers[$tier])) {
            echo "Unknown tier: {$tier}\n";
            echo "Available tiers: " . implode(', ', array_keys($this->tiers)) . "\n";
            return;
        }

        $config = $this->tiers[$tier];

        echo "Upgrading to {$tier}...\n";
        echo "Description: {$config['description']}\n\n";

        foreach ($config['packages'] as $package) {
            echo "Installing {$package}...\n";
            exec("composer require {$package} 2>&1", $output, $exitCode);
            if ($exitCode !== 0) {
                echo "  Warning: composer require {$package} failed\n";
            }
        }

        echo "\nUpgrade to '{$tier}' complete!\n";
    }
}
