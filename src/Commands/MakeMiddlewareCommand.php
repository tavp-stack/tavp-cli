<?php

declare(strict_types=1);

namespace Tavp\Cli\Commands;

use Tavp\Cli\Support\GeneratesFiles;

/**
 * tavp make:middleware — generate a middleware class in app/Middleware.
 *
 * Usage: tavp make:middleware <Name>
 */
class MakeMiddlewareCommand
{
    use GeneratesFiles;

    public function handle(array $args): void
    {
        $name = $args[0] ?? null;

        if ($name === null || str_starts_with($name, '--')) {
            echo "Usage: tavp make:middleware <Name>\n";

            return;
        }

        $class = $this->classBase($name, 'Middleware');

        $content = <<<PHP
<?php

declare(strict_types=1);

namespace App\Middleware;

class {$class}
{
    /**
     * Handle the incoming request. Return true to continue, false to stop.
     */
    public function handle(array \$request, callable \$next): mixed
    {
        // Run logic before the request is handled...

        \$response = \$next(\$request);

        // Run logic after the request is handled...

        return \$response;
    }
}

PHP;

        $this->put("app/Middleware/{$class}.php", $content);
    }
}
