<?php

declare(strict_types=1);

namespace Tavp\Cli\Commands;

use Tavp\Cli\TavpCommand;

class DevboxDownCommand extends TavpCommand
{
    protected string $signature = 'devbox:down';
    protected string $description = 'Stop TAVP DevBox containers (data preserved)';

    public function execute(): int
    {
        $devbox_dir = $this->get_devbox_dir();

        if (!file_exists($devbox_dir . '/docker-compose.yml')) {
            $this->error('DevBox not found.');
            return 1;
        }

        $this->info('Stopping TAVP DevBox...');
        $this->run_cmd('docker compose down', $devbox_dir);
        $this->info('DevBox stopped. Data preserved in volumes.');

        return 0;
    }

    private function get_devbox_dir(): string
    {
        $project_root = getcwd();
        $local = $project_root . '/tavp-devbox';
        $global = dirname(__DIR__, 3) . '/tavp-devbox';

        if (is_dir($local)) return $local;
        if (is_dir($global)) return $global;

        $dir = $project_root;
        while ($dir !== dirname($dir)) {
            if (is_dir($dir . '/tavp-devbox')) return $dir . '/tavp-devbox';
            $dir = dirname($dir);
        }

        return $global;
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
