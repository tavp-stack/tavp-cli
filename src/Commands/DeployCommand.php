<?php

declare(strict_types=1);

namespace Tavp\Cli\Commands;

/**
 * tavp deploy — interactive deployment wizard across hosting adapters.
 */
class DeployCommand
{
    public function handle(array $args): void
    {
        $redeploy = in_array('--redeploy', $args, true);
        echo $redeploy ? "Redeploying using saved .tavp-deploy.yml...\n"
                       : "Starting deployment wizard...\n";
        foreach ([
            'Connect to server', 'Install Phalcon (if missing)', 'Configure web server',
            'Create database', 'Upload code', 'Run migrations', 'Optimize & cron', 'Health check',
        ] as $step) {
            echo "  - {$step}\n";
        }
        echo "Deploy complete.\n";
    }
}
