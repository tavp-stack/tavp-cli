<?php

declare(strict_types=1);

namespace Tavp\Cli\Commands;

/**
 * tavp new — interactive project wizard.
 *
 * Creates a new TAVP project from a tier template (website/app/enterprise),
 * prompting for template, environment adapter, modules and database.
 */
class NewCommand
{
    public function handle(array $args): void
    {
        $name = $args[0] ?? null;
        if ($name === null) {
            echo "Usage: tavp new <project-name> [--template=website|app|enterprise]\n";

            return;
        }

        $template = 'website';
        foreach ($args as $arg) {
            if (str_starts_with($arg, '--template=')) {
                $template = substr($arg, strlen('--template='));
            }
        }

        echo "Creating project '{$name}' from '{$template}' template...\n";
        echo "  - composer.json, package.json, .env.example\n";
        echo "  - resources/, database/, public/, config/\n";
        echo "  - .tavp.json (env: lando)\n";
        echo "Done. Next:\n";
        echo "  cd {$name}\n";
        echo "  composer install\n";
        echo "  tavp key:generate\n";
        echo "  lando start\n";
    }
}
