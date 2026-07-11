<?php

declare(strict_types=1);

namespace Tavp\Cli\Commands;

/**
 * tavp hub:make:resource — generate a TAVPhub admin resource.
 */
class HubMakeResourceCommand
{
    public function handle(array $args): void
    {
        $name = $args[0] ?? '';

        if ($name === '') {
            echo "Usage: tavp hub:make:resource <Name>\n";
            echo "Example: tavp hub:make:resource Product\n";
            return;
        }

        $dir = getcwd();
        $lcName = strtolower($name);
        $plural = $lcName . 's';

        // Check if hub config exists
        $configFile = $dir . '/config/hub.php';
        if (!is_file($configFile)) {
            echo "Error: config/hub.php not found. Run `tavp hub:install` first.\n";
            return;
        }

        // Generate resource class
        $resourceDir = $dir . '/app/Resources';
        if (!is_dir($resourceDir)) {
            mkdir($resourceDir, 0755, true);
        }

        $resourceFile = "{$resourceDir}/{$name}Resource.php";
        $content = <<<PHP
<?php

declare(strict_types=1);

namespace App\Resources;

use Tavp\Hub\Resource;

class {$name}Resource extends Resource
{
    public function label(): string
    {
        return '{$plural}';
    }

    public function model(): string
    {
        return \App\Models\{$name}::class;
    }

    public function columns(): array
    {
        return [
            ['field' => 'id', 'label' => 'ID'],
            ['field' => 'name', 'label' => 'Name'],
            ['field' => 'created_at', 'label' => 'Created'],
        ];
    }

    public function fields(): array
    {
        return [
            ['name' => 'name', 'type' => 'text', 'required' => true],
        ];
    }
}
PHP;

        file_put_contents($resourceFile, $content);
        echo "Created {$resourceFile}\n";

        // Update hub config
        $configContent = file_get_contents($configFile);
        $resourceEntry = "        '{$lcName}' => [\\App\\Resources\\{$name}Resource::class],";

        if (str_contains($configContent, "'{$lcName}'")) {
            echo "Resource '{$lcName}' already in config.\n";
        } else {
            // Add to resources array
            $configContent = str_replace(
                "'resources' => [",
                "'resources' => [\n{$resourceEntry}",
                $configContent
            );
            file_put_contents($configFile, $configContent);
            echo "Added to config/hub.php\n";
        }

        echo "Resource '{$name}' created!\n";
    }
}
