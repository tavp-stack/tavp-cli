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

                // Register database connection
                $app->bind('db', function () use ($root) {
                    $config = [];
                    if (is_file($root . '/config/database.php')) {
                        $config = require $root . '/config/database.php';
                    }

                    $default = $config['default'] ?? 'mysql';
                    $conn = $config['connections'][$default] ?? [];
                    $host = $conn['host'] ?? '127.0.0.1';
                    $port = $conn['port'] ?? 3306;
                    $dbname = $conn['dbname'] ?? $conn['database'] ?? '';
                    $username = $conn['username'] ?? '';
                    $password = $conn['password'] ?? '';

                    return new \PDO(
                        "mysql:host={$host};port={$port};dbname={$dbname};charset=utf8mb4",
                        $username,
                        $password,
                        [\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION]
                    );
                });
            }
        }

        echo "Checking for scheduled content...\n";

        try {
            $db = app('db');

            $result = $db->query("SELECT id, type, data FROM contents WHERE status = 'scheduled'");
            $scheduled = $result->fetchAll();

            $published = 0;

            foreach ($scheduled as $row) {
                $data = json_decode($row['data'] ?? '{}', true);
                $publishedAt = $data['published_at'] ?? null;

                if ($publishedAt !== null && strtotime($publishedAt) <= time()) {
                    $data['status'] = 'published';
                    $db->prepare("UPDATE contents SET data = ?, updated_at = ? WHERE id = ?")
                        ->execute([json_encode($data), date('Y-m-d H:i:s'), $row['id']]);

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
