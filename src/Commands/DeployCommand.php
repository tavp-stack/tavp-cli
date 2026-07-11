<?php

declare(strict_types=1);

namespace Tavp\Cli\Commands;

/**
 * tavp deploy — deploy the application.
 */
class DeployCommand
{
    public function handle(array $args): void
    {
        $env = $args[0] ?? 'production';
        $dir = getcwd();

        echo "Deploying to {$env}...\n\n";

        // Check if deploy config exists
        $deployFile = $dir . '/.tavp-deploy.yml';
        if (is_file($deployFile)) {
            echo "Using deployment config from .tavp-deploy.yml\n";
            $config = yaml_parse_file($deployFile);
            $provider = $config['provider'] ?? 'vercel';
            echo "Provider: {$provider}\n";
        } else {
            echo "No .tavp-deploy.yml found. Using default deployment.\n";
            $provider = 'vercel';
        }

        // Pre-deploy checks
        echo "\nPre-deploy checks:\n";

        // Check for uncommitted changes
        exec('git status --porcelain', $statusOutput);
        if (!empty($statusOutput)) {
            echo "  ⚠ Uncommitted changes detected. Run `tavp push` first.\n";
            return;
        }
        echo "  ✓ No uncommitted changes\n";

        // Check if vendor is up to date
        if (is_file($dir . '/composer.lock')) {
            exec('composer install --no-dev --optimize-autoloader 2>&1', $output, $exitCode);
            if ($exitCode === 0) {
                echo "  ✓ Dependencies installed\n";
            }
        }

        // Deploy based on provider
        echo "\nDeploying...\n";

        switch ($provider) {
            case 'vercel':
                exec('vercel --prod 2>&1', $output, $exitCode);
                break;
            case 'forge':
                exec('git push forge main 2>&1', $output, $exitCode);
                break;
            case 'manual':
                echo "Manual deployment. Push to your server.\n";
                $exitCode = 0;
                break;
            default:
                exec("git push {$provider} main 2>&1", $output, $exitCode);
                break;
        }

        foreach ($output as $line) {
            echo "  {$line}\n";
        }

        if ($exitCode === 0) {
            echo "\nDeployment successful!\n";
        } else {
            echo "\nDeployment failed. Check the output above.\n";
        }
    }
}
