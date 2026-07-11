<?php

declare(strict_types=1);

namespace Tavp\Cli\Commands;

/**
 * tavp schedule:run — run scheduled tasks (publish, cleanup, etc.).
 *
 * Run periodically via cron: * * * * * cd /path && tavp schedule:run
 */
class ScheduleRunCommand
{
    public function handle(array $args): void
    {
        $root = getcwd() ?: '.';
        if (is_file($root . '/vendor/autoload.php')) {
            require_once $root . '/vendor/autoload.php';
        }

        echo "Running scheduled tasks...\n\n";

        $tasks = 0;

        // 1. Publish scheduled content
        $tasks += $this->publishScheduled();

        // 2. Cleanup old revisions
        $tasks += $this->cleanupRevisions();

        // 3. Cleanup expired OTP codes
        $tasks += $this->cleanupOtpCodes();

        echo "\nDone! Ran {$tasks} task(s).\n";
    }

    private function publishScheduled(): int
    {
        echo "Checking scheduled content...\n";

        try {
            $db = app('db');
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

            return $published;
        } catch (\Throwable $e) {
            echo "  Error: {$e->getMessage()}\n";
            return 0;
        }
    }

    private function cleanupRevisions(): int
    {
        echo "Cleaning up old revisions...\n";

        $root = getcwd() ?: '.';
        $revDir = $root . '/storage/cms/revisions';

        if (!is_dir($revDir)) {
            return 0;
        }

        $limit = 50; // Keep last 50 revisions per record
        $cleaned = 0;

        $typeDirs = glob($revDir . '/*') ?: [];
        foreach ($typeDirs as $typeDir) {
            if (!is_dir($typeDir)) {
                continue;
            }

            $recordDirs = glob($typeDir . '/*') ?: [];
            foreach ($recordDirs as $recordDir) {
                $files = glob($recordDir . '/*.json') ?: [];
                if (count($files) > $limit) {
                    usort($files, fn ($a, $b) => filemtime($a) <=> filemtime($b));
                    $toDelete = array_slice($files, 0, count($files) - $limit);
                    foreach ($toDelete as $file) {
                        unlink($file);
                        $cleaned++;
                    }
                }
            }
        }

        if ($cleaned > 0) {
            echo "  Cleaned {$cleaned} old revision files\n";
        }

        return $cleaned;
    }

    private function cleanupOtpCodes(): int
    {
        echo "Cleaning up expired OTP codes...\n";

        try {
            $db = app('db');
            $result = $db->execute(
                "DELETE FROM otp_codes WHERE expires_at < NOW()",
                []
            );

            return $result;
        } catch (\Throwable $e) {
            echo "  Error: {$e->getMessage()}\n";
            return 0;
        }
    }
}
