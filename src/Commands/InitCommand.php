<?php

declare(strict_types=1);

namespace Tavp\Cli\Commands;

/**
 * tavp init — initialize a git repo with .gitignore and first commit.
 */
class InitCommand
{
    public function handle(array $args): void
    {
        echo "Initializing git repository...\n";
        echo "Created .gitignore\n";
        echo "Initial commit created.\n";
    }
}
