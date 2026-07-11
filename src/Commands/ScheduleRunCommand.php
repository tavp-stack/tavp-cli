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
        echo "Running scheduled tasks...\n\n";

        $ran = 0;

        // Publish scheduled content (CMS).
        if (class_exists(\Tavp\Cms\Publishing\PublishScheduler::class)) {
            $ran += $this->runPublishScheduler();
        }

        // Custom tasks can be added here by the application.

        if ($ran === 0) {
            echo "No scheduled tasks to run.\n";
        } else {
            echo "\nCompleted {$ran} scheduled task(s).\n";
        }
    }

    private function runPublishScheduler(): int
    {
        try {
            $bread = app()->getService(\Tavp\Cms\Bread\BreadManager::class);
            $scheduler = new \Tavp\Cms\Publishing\PublishScheduler($bread);
            $published = $scheduler->publishDue();

            foreach ($published as $item) {
                echo "  Published: [{$item['type']}] {$item['title']} (ID: {$item['id']})\n";
            }

            return count($published);
        } catch (\Throwable $e) {
            echo "  Publish scheduler error: {$e->getMessage()}\n";
            return 0;
        }
    }
}
