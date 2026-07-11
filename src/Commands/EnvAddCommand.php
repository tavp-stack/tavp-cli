<?php

declare(strict_types=1);

namespace Tavp\Cli\Commands;

/**
 * tavp env:add — add an environment adapter (lando, docker, etc.).
 */
class EnvAddCommand
{
    private array $adapters = [
        'lando' => [
            'description' => 'Lando local development',
            'files' => ['.lando.yml'],
        ],
        'docker' => [
            'description' => 'Docker Compose',
            'files' => ['docker-compose.yml', 'Dockerfile'],
        ],
        'vagrant' => [
            'description' => 'Vagrant',
            'files' => ['Vagrantfile'],
        ],
    ];

    public function handle(array $args): void
    {
        $env = $args[0] ?? '';

        if ($env === '') {
            echo "Usage: tavp env:add <adapter>\n";
            echo "Available adapters: " . implode(', ', array_keys($this->adapters)) . "\n";
            return;
        }

        if (!isset($this->adapters[$env])) {
            echo "Unknown adapter: {$env}\n";
            echo "Available adapters: " . implode(', ', array_keys($this->adapters)) . "\n";
            return;
        }

        $adapter = $this->adapters[$env];

        echo "Adding environment adapter: {$env}\n";
        echo "Description: {$adapter['description']}\n";

        // Check if adapter files already exist
        foreach ($adapter['files'] as $file) {
            if (is_file(getcwd() . '/' . $file)) {
                echo "  {$file} already exists, skipping.\n";
            } else {
                echo "  {$file} — needs to be created manually.\n";
            }
        }

        echo "Environment adapter '{$env}' configured.\n";
    }
}
