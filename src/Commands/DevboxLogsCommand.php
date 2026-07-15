<?php

declare(strict_types=1);

namespace Tavp\Cli\Commands;

use Tavp\Cli\TavpCommand;

class DevboxLogsCommand extends TavpCommand
{
    protected string $signature = 'devbox:logs';
    protected string $description = 'Follow TAVP DevBox logs';

    public function execute(): int
    {
        $service = $this->get_arg(0) ?? 'app';

        $this->info("Following logs for: {$service}");
        $this->info('Press Ctrl+C to stop.');
        $this->info('');

        passthru("docker compose -f {$this->get_devbox_dir()}/docker-compose.yml logs -f {$service}");

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
}
