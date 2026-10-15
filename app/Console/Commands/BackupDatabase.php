<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use ZipArchive;

class BackupDatabase extends Command
{
    protected $signature = 'aurevia:backup
                            {--keep=14 : Days of backups to retain}';

    protected $description = 'Dump the database and uploaded files to storage/app/backups, then prune old backups.';

    public function handle(): int
    {
        $dir = storage_path('app/backups');
        File::ensureDirectoryExists($dir);

        $stamp = now()->format('Y-m-d-His');
        $db = config('database.connections.mysql');
        $dumpFile = "{$dir}/{$db['database']}-{$stamp}.sql";

        // --- Database dump ----------------------------------------------------
        $mysqldump = env('MYSQLDUMP_PATH', 'mysqldump');
        $cmd = sprintf(
            '%s --host=%s --port=%s --user=%s --password=%s --single-transaction --routines %s > %s',
            escapeshellarg($mysqldump),
            escapeshellarg((string) ($db['host'] ?? '127.0.0.1')),
            escapeshellarg((string) ($db['port'] ?? '3306')),
            escapeshellarg((string) ($db['username'] ?? '')),
            escapeshellarg((string) ($db['password'] ?? '')),
            escapeshellarg((string) $db['database']),
            escapeshellarg($dumpFile)
        );

        exec($cmd.' 2>&1', $out, $code);

        // On Windows shells redirection handling differs; retry via proc if needed.
        if ($code !== 0 && ! str_contains($cmd, 'cmd /C')) {
            exec('cmd /C '.$cmd.' 2>&1', $out, $code);
        }

        if ($code !== 0 || ! File::exists($dumpFile) || File::size($dumpFile) === 0) {
            $this->error('Database dump failed: '.implode("\n", $out));
            @File::delete($dumpFile);

            return self::FAILURE;
        }
        $this->info('Database dumped: '.basename($dumpFile));

        // --- Uploaded files archive ------------------------------------------
        $public = storage_path('app/public');
        if (File::isDirectory($public) && count(File::allFiles($public)) > 0) {
            $zipFile = "{$dir}/files-{$stamp}.zip";
            $zip = new ZipArchive;
            if ($zip->open($zipFile, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
                foreach (File::allFiles($public) as $file) {
                    $zip->addFile($file->getPathname(), $file->getRelativePath());
                }
                $zip->close();
                $this->info('Uploaded files archived: '.basename($zipFile));
            }
        }

        // --- Prune old backups --------------------------------------------------
        $keep = (int) $this->option('keep');
        $cutoff = now()->subDays($keep)->getTimestamp();
        $pruned = 0;
        foreach (File::files($dir) as $file) {
            if ($file->getMTime() < $cutoff) {
                File::delete($file->getPathname());
                $pruned++;
            }
        }
        if ($pruned) {
            $this->info("Pruned {$pruned} backup(s) older than {$keep} days.");
        }

        $this->info('Backup complete. Store copies off-server in production.');

        return self::SUCCESS;
    }
}
