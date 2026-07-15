<?php

declare(strict_types=1);

namespace Tavp\Cli\Commands;

use Tavp\Cli\TavpCommand;

class DevboxShellCommand extends TavpCommand
{
    protected string $signature = 'devbox:shell';
    protected string $description = 'Enter TAVP DevBox container shell';

    public function execute(): int
    {
        $this->info('Entering TAVP DevBox shell...');
        $this->info('Type "exit" to return to host.');
        $this->info('');

        passthru('docker exec -it tavp-app /bin/sh');

        return 0;
    }
}
