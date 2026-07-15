<?php

declare(strict_types=1);

namespace Tavp\Cli\Commands;

use Tavp\Cli\TavpCommand;

class DevboxUpCommand extends TavpCommand
{
    protected string $signature = 'devbox:up';
    protected string $description = 'Start TAVP DevBox containers';

    public function execute(): int
    {
        $devbox_dir = $this->get_devbox_dir();

        if (!$this->is_docker_running()) {
            $this->error('Docker is not running. Please start Docker Desktop first.');
            return 1;
        }

        if (!file_exists($devbox_dir . '/docker-compose.yml')) {
            $this->error('DevBox not found. Run: tavp devbox:init');
            return 1;
        }

        // Build image if not exists
        if (!$this->image_exists('tavp/devbox:8.3')) {
            $this->info('Building TAVP DevBox image (first time only, ~5 min)...');
            $this->run_cmd('docker compose build', $devbox_dir);
        }

        $this->info('Starting TAVP DevBox...');
        $this->run_cmd('docker compose up -d', $devbox_dir);

        $this->info('');
        $this->info('  TAVP DevBox is running!');
        $this->info('  ─────────────────────────────────────');
        $this->info('  App:        http://localhost:8000');
        $this->info('  Mailpit:    http://localhost:8025');
        $this->info('  phpMyAdmin: http://localhost:8080');
        $this->info('  ─────────────────────────────────────');
        $this->info('  Commands: tavp devbox:logs | devbox:shell | devbox:down');
        $this->info('');

        return 0;
    }

    private function get_devbox_dir(): string
    {
        // Try project root first, then global
        $project_root = getcwd();
        $local = $project_root . '/tavp-devbox';
        $global = dirname(__DIR__, 3) . '/tavp-devbox';

        if (is_dir($local)) return $local;
        if (is_dir($global)) return $global;

        // Fallback: search up
        $dir = $project_root;
        while ($dir !== dirname($dir)) {
            if (is_dir($dir . '/tavp-devbox')) return $dir . '/tavp-devbox';
            $dir = dirname($dir);
        }

        return $global;
    }

    private function is_docker_running(): bool
    {
        exec('docker info 2>&1', $output, $code);
        return $code === 0;
    }

    private function image_exists(string $image): bool
    {
        exec("docker image inspect {$image} 2>&1", $output, $code);
        return $code === 0;
    }

    private function run_cmd(string $cmd, string $cwd): void
    {
        $process = proc_open($cmd, [
            0 => ['pipe', 'r'],
            1 => ['pipe', 'w'],
            2 => ['pipe', 'w'],
        ], $pipes, $cwd);

        while (!feof($pipes[1])) {
            echo fgets($pipes[1]);
        }

        proc_close($process);
    }
}
