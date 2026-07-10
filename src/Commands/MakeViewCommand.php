<?php

declare(strict_types=1);

namespace Tavp\Cli\Commands;

use Tavp\Cli\Support\GeneratesFiles;

/**
 * tavp make:view — generate a Volt view template in resources/views.
 *
 * Usage: tavp make:view <name> [--layout=layouts.app]
 * Dotted names become folders: make:view users.index -> resources/views/users/index.volt
 */
class MakeViewCommand
{
    use GeneratesFiles;

    public function handle(array $args): void
    {
        $name = $args[0] ?? null;

        if ($name === null || str_starts_with($name, '--')) {
            echo "Usage: tavp make:view <name> [--layout=layouts.app]\n";

            return;
        }

        $layout = $this->option($args, '--layout', 'layouts.app');
        $layoutPath = str_replace('.', '/', $layout);
        $relPath = 'resources/views/' . str_replace('.', '/', $name) . '.volt';

        $title = ucwords(str_replace(['.', '-', '_'], ' ', $name));

        $content = <<<VOLT
{% extends '{$layoutPath}' %}

{% block content %}
<div class="px-4 py-6 sm:px-0">
    <h1 class="text-2xl font-bold text-gray-900">{$title}</h1>
    <p class="mt-2 text-gray-600">This is the {$name} view.</p>
</div>
{% endblock %}

VOLT;

        $this->put($relPath, $content);
    }

    private function option(array $args, string $key, string $default): string
    {
        foreach ($args as $arg) {
            if (str_starts_with($arg, $key . '=')) {
                return substr($arg, strlen($key) + 1);
            }
        }

        return $default;
    }
}
