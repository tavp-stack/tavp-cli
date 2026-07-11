<?php

declare(strict_types=1);

namespace Tavp\Cli\Commands;

/**
 * tavp push — commit and push changes to remote.
 */
class PushCommand
{
    public function handle(array $args): void
    {
        $message = $args[0] ?? null;
        $branch = 'main';

        // Check if there are changes
        exec('git status --porcelain', $statusOutput);
        if (empty($statusOutput)) {
            echo "No changes to commit.\n";
            return;
        }

        // Stage all changes
        echo "Staging changes...\n";
        exec('git add .', $output, $exitCode);
        if ($exitCode !== 0) {
            echo "Error: git add failed.\n";
            return;
        }

        // Commit
        if ($message === null) {
            $message = 'Update: ' . date('Y-m-d H:i:s');
        }

        echo "Committing: {$message}\n";
        exec("git commit -m \"{$message}\" 2>&1", $output, $exitCode);
        foreach ($output as $line) {
            echo "  {$line}\n";
        }

        if ($exitCode !== 0) {
            echo "Error: git commit failed.\n";
            return;
        }

        // Push
        echo "Pushing to origin/{$branch}...\n";
        exec("git push origin {$branch} 2>&1", $output, $exitCode);
        foreach ($output as $line) {
            echo "  {$line}\n";
        }

        if ($exitCode !== 0) {
            echo "Error: git push failed. Run `tavp remote:add <url>` to add a remote.\n";
            return;
        }

        echo "Done!\n";
    }
}
