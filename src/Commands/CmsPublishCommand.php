<?php

declare(strict_types=1);

namespace Tavp\Cli\Commands;

/**
 * tavp cms:publish — publish content whose published_at date has arrived.
 *
 * Run periodically via cron: tavp cms:publish
 */
class CmsPublishCommand
{
    public function handle(array $args): void
    {
        $root = getcwd() ?: '.';
        if (is_file($root . '/vendor/autoload.php')) {
            require_once $root . '/vendor/autoload.php';
        }

        // Bootstrap app
        if (class_exists(\Tavp\Core\Application::class)) {
            try {
                \Tavp\Core\Application::getInstance();
            } catch (\RuntimeException) {
                $app = new \Tavp\Core\Application($root);
                $app->bootstrap();
            }
        }

        echo "Checking for scheduled content...\n";

        // Find content with status 'scheduled' or 'draft' where published_at has passed
        try {
            $db = app('db');
            $now = date('Y-m-d H:i:s');

            // Find scheduled content
            $scheduled = $db->query(
                "SELECT id, type, data FROM contents WHERE status = 'scheduled'",
                []
            );

            $published = 0;

            foreach ($scheduled as $row) {
                $data = json_decode($row['data'] ?? '{}', true);
                $publishedAt = $data['published_at'] ?? null;

                if ($publishedAt !== null && strtotime($publishedAt) <= time()) {
                    $data['status'] = 'published';
                    $db->update('contents', [
                        'data' => json_encode($data),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ], ['id' => $row['id']]);

                    $title = $data['title'] ?? $row['type'] . '#' . $row['id'];
                    echo "  Published: {$row['type']}/{$title}\n";
                    $published++;
                }
            }

            echo "\nDone! Published {$published} item(s).\n";
        } catch (\Throwable $e) {
            echo "Error: {$e->getMessage()}\n";
            exit(1);
        }
    }
}
