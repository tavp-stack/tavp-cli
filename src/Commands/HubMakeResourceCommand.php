<?php

declare(strict_types=1);

namespace Tavp\Cli\Commands;

use Tavp\Cli\Support\GeneratesFiles;

/**
 * tavp hub:make:resource — generate a TAVPhub admin resource.
 *
 *   tavp hub:make:resource Product
 *   tavp hub:make:resource Product --model=App\Models\Product --icon=cube
 */
class HubMakeResourceCommand
{
    use GeneratesFiles;

    public function handle(array $args): void
    {
        $name = '';
        $model = null;
        $icon = 'cube';

        foreach ($args as $arg) {
            if (str_starts_with($arg, '--model=')) {
                $model = substr($arg, strlen('--model='));
            } elseif (str_starts_with($arg, '--icon=')) {
                $icon = substr($arg, strlen('--icon='));
            } elseif ($name === '' && !str_starts_with($arg, '--')) {
                $name = $arg;
            }
        }

        if ($name === '') {
            echo "Usage: tavp hub:make:resource <Name> [--model=App\\Models\\Foo] [--icon=cube]\n";
            echo "Example: tavp hub:make:resource Product\n";
            return;
        }

        $studly = $this->studly($name);
        $className = $this->classBase($studly, 'Resource');
        $key = $this->snake(str_replace('Resource', '', $className));
        $pluralLabel = $this->plural($this->snake(str_replace('Resource', '', $className)));
        $modelClass = $model ?? ('App\\Models\\' . str_replace('Resource', '', $className));

        $root = $this->projectRoot();
        if (!is_file($root . '/config/hub.php')) {
            echo "Error: config/hub.php not found. Run `tavp hub:install` first.\n";
            return;
        }

        $resourceDir = $root . '/app/Resources';
        $resourceFile = "{$resourceDir}/{$className}.php";

        $content = $this->stub($className, $pluralLabel, $modelClass, (string) $icon);
        $this->put("app/Resources/{$className}.php", $content);

        // Auto-register unless discovery is enabled.
        $config = file_get_contents($root . '/config/hub.php');
        $discoveryEnabled = $this->discoveryEnabled($config);

        if ($discoveryEnabled) {
            echo "Resource auto-discovered via config('hub.discovery'). No config edit needed.\n";
        } elseif (str_contains($config, "'{$key}'")) {
            echo "Resource '{$key}' already registered in config/hub.php.\n";
        } else {
            $config = str_replace(
                "'resources' => [",
                "'resources' => [\n        '{$key}' => \\App\\Resources\\{$className}::class,",
                $config
            );
            file_put_contents($root . '/config/hub.php', $config);
            echo "Registered '{$key}' in config/hub.php\n";
        }

        echo "Done. Visit /admin/{$key} (after registering routes).\n";
    }

    private function discoveryEnabled(string $config): bool
    {
        if (preg_match("/'discovery'\s*=>\s*true/", $config)) {
            return true;
        }
        if (preg_match("/'discovery'\s*=>\s*\[([^\\]]*)\]/s", $config, $m)) {
            return !preg_match("/'enabled'\s*=>\s*false/", $m[1]);
        }

        return false;
    }

    private function stub(string $className, string $pluralLabel, string $modelClass, string $icon): string
    {
        return <<<PHP
<?php

declare(strict_types=1);

namespace App\Resources;

use Tavp\Hub\Filter;
use Tavp\Hub\Relation;
use Tavp\Hub\Resource;
use Tavp\Hub\ValueMetric;

class {$className} extends Resource
{
    public function label(): string
    {
        return '{$pluralLabel}';
    }

    public function icon(): string
    {
        return '{$icon}';
    }

    public function model(): string
    {
        return \\{$modelClass}::class;
    }

    public function columns(): array
    {
        return [
            ['key' => 'id', 'label' => 'ID', 'sortable' => true],
            ['key' => 'name', 'label' => 'Name', 'sortable' => true, 'searchable' => true],
            ['key' => 'status', 'label' => 'Status', 'sortable' => true, 'badge' => true],
            ['key' => 'created_at', 'label' => 'Created', 'sortable' => false],
        ];
    }

    public function fields(): array
    {
        return [
            ['name' => 'name', 'type' => 'text', 'label' => 'Name', 'required' => true],
            ['name' => 'status', 'type' => 'select', 'label' => 'Status', 'options' => ['draft', 'published']],
            ['name' => 'body', 'type' => 'textarea', 'label' => 'Body'],
        ];
    }

    public function searchableColumns(): array
    {
        return ['name'];
    }

    public function filters(): array
    {
        return [
            (new Filter('status'))->options(['draft', 'published'])->type('select'),
        ];
    }

    public function metrics(): array
    {
        return [
            (new ValueMetric('total', 'Total {$pluralLabel}'))->aggregate('count'),
        ];
    }

    // Relate this resource to another (uncomment + adjust):
    // public function relations(): array
    // {
    //     return [
    //         new Relation('category_id', 'belongsTo', 'categories', 'name', 'category_id'),
    //     ];
    // }
}

PHP;
    }
}
