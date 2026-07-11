<?php

declare(strict_types=1);

namespace Tavp\Cli\Commands;

/**
 * tavp schedule:run — run scheduled tasks.
 *
 * Execute pending scheduled tasks based on their frequency.
 * Run via cron: * * * * * cd /path/to/project && php bin/tavp schedule:run
 */
class ScheduleRunCommand
{
    public function handle(array $args): void
    {
        $this->info('Running scheduled tasks...');
        echo "\n";

        $ran = 0;

        // Publish scheduled content (CMS).
        if (class_exists(\Tavp\Cms\Publishing\PublishScheduler::class)) {
            $ran += $this->runPublishScheduler();
        }

        echo "\n";
        if ($ran === 0) {
            $this->comment('No scheduled tasks to run.');
        } else {
            $this->success("Completed {$ran} scheduled task(s).");
        }
    }

    private function runPublishScheduler(): int
    {
        try {
            $bread = \app()->getService(\Tavp\Cms\Bread\BreadManager::class);
            $scheduler = new \Tavp\Cms\Publishing\PublishScheduler($bread);
            $published = $scheduler->publishDue();

            foreach ($published as $item) {
                $this->line("  <info>Published</info>: [{$item['type']}] {$item['title']} (ID: {$item['id']})");
            }

            return count($published);
        } catch (\Throwable $e) {
            $this->error("  Publish scheduler error: {$e->getMessage()}");
            return 0;
        }
    }

    private function info(string $msg): void
    {
        echo "\033[36m{$msg}\033[0m\n";
    }

    private function comment(string $msg): void
    {
        echo "\033[33m{$msg}\033[0m\n";
    }

    private function line(string $msg): void
    {
        $clean = preg_replace('/<[^>]+>/', '', $msg);
        echo "  {$clean}\n";
    }

    private function success(string $msg): void
    {
        echo "\033[32m✔ {$msg}\033[0m\n";
    }

    private function error(string $msg): void
    {
        echo "\033[31m✘ {$msg}\033[0m\n";
    }
}
