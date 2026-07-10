<?php

declare(strict_types=1);

namespace Tavp\Cli\Commands;

use Tavp\Cli\Support\GeneratesFiles;

/**
 * tavp make:event — generate an event class in app/Events.
 *
 * Usage: tavp make:event <Name>
 */
class MakeEventCommand
{
    use GeneratesFiles;

    public function handle(array $args): void
    {
        $name = $args[0] ?? null;

        if ($name === null || str_starts_with($name, '--')) {
            echo "Usage: tavp make:event <Name>\n";

            return;
        }

        $class = $this->classBase($name, 'Event');

        $content = <<<PHP
<?php

declare(strict_types=1);

namespace App\Events;

class {$class}
{
    /**
     * Create a new event instance. Pass any payload the listeners need.
     */
    public function __construct(
        public readonly array \$payload = []
    ) {
    }
}

PHP;

        $this->put("app/Events/{$class}.php", $content);
    }
}
