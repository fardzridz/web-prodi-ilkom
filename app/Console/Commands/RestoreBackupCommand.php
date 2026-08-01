<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Process\Process;

#[Signature('db:restore {file : Backup file name inside storage/app/backups} {--force : Skip confirmation}')]
#[Description('Restore a MySQL database from a SQL backup file produced by migrate:safe.')]
class RestoreBackupCommand extends Command
{
    private const BACKUP_DIR = 'storage/app/backups';

    public function handle(): int
    {
        $file = (string) $this->argument('file');
        $forced = (bool) $this->option('force');

        $absolutePath = base_path(self::BACKUP_DIR.DIRECTORY_SEPARATOR.$file);

        if (! file_exists($absolutePath)) {
            $this->error("Backup file not found: {$absolutePath}");

            return self::FAILURE;
        }

        $database = DB::connection()->getDatabaseName();
        $driver = DB::connection()->getDriverName();

        if ($driver !== 'mysql') {
            $this->error("Restore currently supports MySQL only. Driver: {$driver}");

            return self::FAILURE;
        }

        if (! $forced && ! $this->confirm("This will overwrite the current data in {$database}. Continue?", false)) {
            $this->error('Aborted by user.');

            return self::FAILURE;
        }

        $command = [
            'mysql',
            '--user='.env('DB_USERNAME', 'root'),
            '--host='.env('DB_HOST', '127.0.0.1'),
            '--port='.env('DB_PORT', '3306'),
            $database,
        ];

        if (filled((string) env('DB_PASSWORD'))) {
            $command[] = '--password='.env('DB_PASSWORD');
        }

        $process = new Process($command);
        $process->setInput(file_get_contents($absolutePath));
        $process->setTimeout(180);

        try {
            $process->run();
        } catch (\Throwable $exception) {
            $this->error('mysql client not available: '.$exception->getMessage());

            return self::FAILURE;
        }

        if (! $process->isSuccessful()) {
            $this->error('Restore failed: '.$process->getErrorOutput());

            return self::FAILURE;
        }

        $this->info("Restored {$database} from {$absolutePath}");

        return self::SUCCESS;
    }
}
