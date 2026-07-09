<?php

declare(strict_types=1);

namespace Tavp\Cli;

/**
 * The root "tavp" command dispatcher.
 *
 * Maps the first CLI argument to a handler class in Tavp\Cli\Commands.
 * Keeps a single, readable entry point for the whole CLI surface.
 */
class TavpCommand
{
    private array $commands = [
        'new' => Commands\NewCommand::class,
        'serve' => Commands\ServeCommand::class,
        'migrate' => Commands\MigrateCommand::class,
        'make:migration' => Commands\MakeMigrationCommand::class,
        'make:controller' => Commands\MakeControllerCommand::class,
        'make:model' => Commands\MakeModelCommand::class,
        'make:view' => Commands\MakeViewCommand::class,
        'make:scaffold' => Commands\MakeScaffoldCommand::class,
        'make:middleware' => Commands\MakeMiddlewareCommand::class,
        'make:seeder' => Commands\MakeSeederCommand::class,
        'make:event' => Commands\MakeEventCommand::class,
        'make:listener' => Commands\MakeListenerCommand::class,
        'make:module' => Commands\MakeModuleCommand::class,
        'key:generate' => Commands\KeyGenerateCommand::class,
        'env:switch' => Commands\EnvSwitchCommand::class,
        'env:add' => Commands\EnvAddCommand::class,
        'env:list' => Commands\EnvListCommand::class,
        'down' => Commands\DownCommand::class,
        'up' => Commands\UpCommand::class,
        'deploy' => Commands\DeployCommand::class,
        'hub:install' => Commands\HubInstallCommand::class,
        'hub:make:resource' => Commands\HubMakeResourceCommand::class,
        'kit:install' => Commands\KitInstallCommand::class,
        'upgrade' => Commands\UpgradeCommand::class,
        'weave:enable' => Commands\WeaveEnableCommand::class,
        'phalcon:install' => Commands\PhalconInstallCommand::class,
        'push' => Commands\PushCommand::class,
        'pull' => Commands\PullCommand::class,
        'init' => Commands\InitCommand::class,
        'remote:add' => Commands\RemoteAddCommand::class,
    ];

    public function run(array $argv): void
    {
        $name = $argv[1] ?? 'help';

        if (in_array($name, ['help', '--help', '-h'], true)) {
            $this->printHelp();

            return;
        }

        if (!isset($this->commands[$name])) {
            echo "Unknown command: {$name}\n";
            $this->printHelp();

            return;
        }

        (new ($this->commands[$name])())->handle(array_slice($argv, 2));
    }

    private function printHelp(): void
    {
        echo "TAVP CLI — Tailwind + Alpine + Volt + Phalcon\n\n";
        echo "Commands:\n";
        foreach (array_keys($this->commands) as $command) {
            echo "  tavp {$command}\n";
        }
    }
}
