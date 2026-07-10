<?php

declare(strict_types=1);

namespace Tavp\Cli\Commands;

use Tavp\Cli\Support\GeneratesFiles;

/**
 * tavp make:listener — generate an event listener in app/Listeners.
 *
 * Usage: tavp make:listener <Name> [--event=SomeEvent]
 */
class MakeListenerCommand
{
    use GeneratesFiles;

    public function handle(array $args): void
    {
        $name = $args[0] ?? null;

        if ($name === null || str_starts_with($name, '--')) {
            echo "Usage: tavp make:listener <Name> [--event=SomeEvent]\n";

            return;
        }

        $class = $this->classBase($name, 'Listener');
        $event = $this->option($args, '--event', '');

        if ($event !== '') {
            $eventClass = $this->classBase($event, 'Event');
            $useLine = "use App\\Events\\{$eventClass};\n";
            $param = "{$eventClass} \$event";
        } else {
            $useLine = '';
            $param = 'object $event';
        }

        $content = <<<PHP
<?php

declare(strict_types=1);

namespace App\Listeners;

{$useLine}
class {$class}
{
    /**
     * Handle the dispatched event.
     */
    public function handle({$param}): void
    {
        // React to the event...
    }
}

PHP;

        $this->put("app/Listeners/{$class}.php", $content);
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
