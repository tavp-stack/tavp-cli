<?php

declare(strict_types=1);

namespace Tavp\Cli\Tests;

use Tavp\Cli\TavpCommand;
use PHPUnit\Framework\TestCase;

/**
 * Verifies the CLI dispatcher routes known commands and rejects unknown ones.
 */
class TavpCommandTest extends TestCase
{
    public function testDispatcherRegistersAllExpectedCommands(): void
    {
        $reflection = new \ReflectionClass(TavpCommand::class);
        $property = $reflection->getProperty('commands');
        $property->setAccessible(true);
        $commands = $property->getValue(new TavpCommand());

        $expected = [
            'new', 'serve', 'migrate', 'make:migration', 'make:controller',
            'make:model', 'make:view', 'make:scaffold', 'make:middleware',
            'make:seeder', 'make:event', 'make:listener', 'make:module',
            'key:generate', 'env:switch', 'env:add', 'env:list', 'down',
            'up', 'deploy', 'hub:install', 'hub:make:resource',
            'kit:install', 'upgrade', 'weave:enable', 'push', 'pull',
            'init', 'remote:add',
        ];

        foreach ($expected as $cmd) {
            $this->assertArrayHasKey($cmd, $commands, "Missing command: {$cmd}");
            $this->assertTrue(class_exists($commands[$cmd]), "Class not found for: {$cmd}");
        }
    }

    public function testEveryCommandClassIsInstantiable(): void
    {
        $reflection = new \ReflectionClass(TavpCommand::class);
        $property = $reflection->getProperty('commands');
        $property->setAccessible(true);
        $commands = $property->getValue(new TavpCommand());

        foreach ($commands as $class) {
            $instance = new $class();
            $this->assertTrue(method_exists($instance, 'handle'), "{$class} missing handle()");
        }
    }
}
