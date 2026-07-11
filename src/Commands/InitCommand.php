<?php

declare(strict_types=1);

namespace Tavp\Cli\Commands;

/**
 * tavp init — initialize git repo with .gitignore and first commit.
 */
class InitCommand
{
    public function handle(array $args): void
    {
        $dir = getcwd();

        // Check if already a git repo
        if (is_dir($dir . '/.git')) {
            echo "Git repository already initialized.\n";
            return;
        }

        // Create .gitignore if not exists
        $gitignore = $dir . '/.gitignore';
        if (!is_file($gitignore)) {
            $content = <<<'GITIGNORE'
/vendor/
.env
.env.local
/storage/compiled/
/storage/logs/
/node_modules/
/dist/
*.cache
GITIGNORE;
            file_put_contents($gitignore, $content);
            echo "Created .gitignore\n";
        }

        // Initialize git
        exec('git init', $output, $exitCode);
        if ($exitCode === 0) {
            echo "Initialized git repository\n";
        } else {
            echo "Error: git init failed\n";
            return;
        }

        // Stage files
        exec('git add .', $output, $exitCode);
        if ($exitCode === 0) {
            echo "Staged all files\n";
        }

        // First commit
        exec('git commit -m "Initial commit: TAVP Stack project"', $output, $exitCode);
        if ($exitCode === 0) {
            echo "Created first commit\n";
        }

        echo "Done! Run `tavp remote:add <url>` to add a remote.\n";
    }
}
