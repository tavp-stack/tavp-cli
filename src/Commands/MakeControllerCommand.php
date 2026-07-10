<?php

declare(strict_types=1);

namespace Tavp\Cli\Commands;

use Tavp\Cli\Support\GeneratesFiles;

/**
 * tavp make:controller — generate a controller in app/Controllers.
 *
 * Usage: tavp make:controller <Name> [--api] [--resource]
 */
class MakeControllerCommand
{
    use GeneratesFiles;

    public function handle(array $args): void
    {
        $name = $args[0] ?? null;

        if ($name === null || str_starts_with($name, '--')) {
            echo "Usage: tavp make:controller <Name> [--api] [--resource]\n";

            return;
        }

        $api = in_array('--api', $args, true);
        $resource = in_array('--resource', $args, true);

        $class = $this->classBase($name, 'Controller');
        $namespace = $api ? 'App\\Controllers\\Api' : 'App\\Controllers';
        $relDir = $api ? 'app/Controllers/Api' : 'app/Controllers';

        $methods = $resource ? $this->resourceMethods() : $this->stubMethods();

        $content = <<<PHP
<?php

declare(strict_types=1);

namespace {$namespace};

class {$class}
{
{$methods}
}

PHP;

        $this->put("{$relDir}/{$class}.php", $content);
    }

    private function stubMethods(): string
    {
        return <<<'PHP'
    public function index(): string
    {
        return json_encode(['data' => []]);
    }

    public function show(int $id): string
    {
        return json_encode(['data' => ['id' => $id]]);
    }
PHP;
    }

    private function resourceMethods(): string
    {
        return <<<'PHP'
    public function index(): string
    {
        return json_encode(['data' => []]);
    }

    public function create(): string
    {
        return '';
    }

    public function store(): string
    {
        return json_encode(['message' => 'Created']);
    }

    public function show(int $id): string
    {
        return json_encode(['data' => ['id' => $id]]);
    }

    public function edit(int $id): string
    {
        return '';
    }

    public function update(int $id): string
    {
        return json_encode(['message' => 'Updated']);
    }

    public function destroy(int $id): string
    {
        return json_encode(['message' => 'Deleted']);
    }
PHP;
    }
}
