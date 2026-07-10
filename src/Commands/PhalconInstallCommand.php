<?php

declare(strict_types=1);

namespace Tavp\Cli\Commands;

/**
 * tavp phalcon:install — install the Phalcon 5 C-extension.
 *
 * Dispatches the bundled install-phalcon.sh script. This is the single
 * command that removes Phalcon's biggest adoption barrier: compiling
 * the extension. One command, any Debian/Ubuntu environment.
 */
class PhalconInstallCommand
{
    public function handle(array $args): void
    {
        $phpVersion = $args[0] ?? '';
        $phalconVersion = $args[1] ?? '5.16.0';

        $script = __DIR__ . '/../../scripts/install-phalcon.sh';

        if (!is_file($script)) {
            echo "ERROR: install-phalcon.sh not found at {$script}\n";

            return;
        }

        // Always pass both positional args. When phpVersion is empty we pass
        // a quoted empty string so the script's ${1:-auto-detect} kicks in,
        // instead of the phalcon version sliding into the PHP-version slot.
        $arg = escapeshellarg($phpVersion) . ' ' . escapeshellarg($phalconVersion);

        echo "Running Phalcon installer...\n";
        passthru('bash ' . escapeshellarg($script) . ' ' . $arg);
    }
}
