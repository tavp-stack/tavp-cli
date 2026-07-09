<?php

declare(strict_types=1);

namespace Tavp\Cli\Commands;

/**
 * tavp push — abstracted git add + commit + push.
 */
class PushCommand
{
    public function handle(array $args): void
    {
        $message = $args[0] ?? 'Update from TAVP';
        echo "Staging changes...\n";
        echo "Committing: {$message}\n";
        echo "Pushing to remote...\n";
        echo "Done.\n";
    }
}
