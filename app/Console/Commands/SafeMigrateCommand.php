<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Process\Process;

#[Signature('migrate:safe {--fresh : Drop all tables before migrating} {--seed : Run seeders after migration} {--force : Skip confirmation prompts}')]
#[Description('Migrate with safety: auto backup existing data and require confirmation before destructive operations.')]
class SafeMigrateCommand extends Command
{
    private const BACKUP_DIR = 'storage/app/backups';

    public function handle(): int
    {
        $database = DB::connection()->getDatabaseName();
        $isFresh = (bool) $this->option('fresh');
        $withSeed = (bool) $this->option('seed');
        $forced = (bool) $this->option('force');

        $this->info("Database target: <comment>{$database}</comment>");

        if (! $this->ensureBackupDirectory()) {
            return self::FAILURE;
        }

        $tables = $this->existingTables();

        if ($tables === []) {
            $this->info('Database is empty. Running migrate without destructive operation.');

            return $this->runMigrate($withSeed);
        }

        $this->warn('Database already contains '.count($tables).' table(s): '.implode(', ', $tables));

        if (! $isFresh) {
            $this->info('Non-destructive migrate selected. Only running pending migrations.');

            return $this->runMigrate($withSeed);
        }

        $backupPath = $this->makeBackup($database, $forced);

        if ($backupPath === null) {
            return self::FAILURE;
        }

        $this->info("Backup saved at: <comment>{$backupPath}</comment>");

        if (! $forced && ! $this->confirm('This will DROP all tables in '.$database.'. Continue?', false)) {
            $this->error('Aborted by user.');

            return self::FAILURE;
        }

        return $this->runMigrate($withSeed);
    }

    private function ensureBackupDirectory(): bool
    {
        $path = base_path(self::BACKUP_DIR);

        if (! is_dir($path) && ! @mkdir($path, 0755, true) && ! is_dir($path)) {
            $this->error("Cannot create backup directory: {$path}");

            return false;
        }

        return true;
    }

    /**
     * @return list<string>
     */
    private function existingTables(): array
    {
        $driver = DB::connection()->getDriverName();

        if ($driver === 'mysql') {
            $database = DB::connection()->getDatabaseName();

            $rows = DB::select('SHOW TABLES');

            if ($rows === []) {
                return [];
            }

            $first = (array) $rows[0];
            $column = array_key_first($first) ?: null;

            if ($column === null) {
                return [];
            }

            return array_map(
                fn (object $row): string => (string) ($row->{$column} ?? ''),
                $rows,
            );
        }

        if ($driver === 'sqlite') {
            $rows = DB::select("SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%'");

            return array_map(fn (object $row): string => (string) $row->name, $rows);
        }

        return [];
    }

    private function makeBackup(string $database, bool $forced): ?string
    {
        $filename = sprintf('%s_%s.sql', $database, now()->format('Ymd_His'));
        $absolutePath = base_path(self::BACKUP_DIR.DIRECTORY_SEPARATOR.$filename);

        $driver = DB::connection()->getDriverName();

        if ($driver === 'mysql') {
            $command = [
                'mysqldump',
                '--user='.env('DB_USERNAME', 'root'),
                '--host='.env('DB_HOST', '127.0.0.1'),
                '--port='.env('DB_PORT', '3306'),
                '--single-transaction',
                '--routines',
                $database,
            ];

            if (filled((string) env('DB_PASSWORD'))) {
                $command[] = '--password='.env('DB_PASSWORD');
            }

            $process = new Process($command);
            $process->setTimeout(120);

            try {
                $process->run();

                if (! $process->isSuccessful()) {
                    $this->error('mysqldump failed: '.$process->getErrorOutput());

                    return $forced ? $this->writeSqliteFallback($absolutePath, $database) : null;
                }
            } catch (\Throwable $exception) {
                $this->warn('mysqldump not available: '.$exception->getMessage());

                return $forced ? $this->writeSqliteFallback($absolutePath, $database) : null;
            }

            file_put_contents($absolutePath, $process->getOutput());

            return $absolutePath;
        }

        if ($driver === 'sqlite') {
            $source = DB::connection()->getDatabaseName();

            if (! file_exists($source)) {
                $this->error("SQLite file not found: {$source}");

                return null;
            }

            if (! copy($source, $absolutePath)) {
                $this->error("Failed to copy SQLite file to {$absolutePath}");

                return null;
            }

            return $absolutePath;
        }

        $this->error("Unsupported driver: {$driver}");

        return null;
    }

    private function writeSqliteFallback(string $path, string $database): string
    {
        file_put_contents(
            $path,
            "-- Backup fallback (driver unsupported): {$database} @ ".now()->toDateTimeString()."\n",
        );

        $this->warn('Wrote empty backup stub. Manual export required.');

        return $path;
    }

    private function runMigrate(bool $withSeed): int
    {
        $arguments = $this->option('fresh') ? ['--fresh' => true] : [];
        $exit = Artisan::call('migrate', $arguments, $this->output);

        if ($exit !== 0) {
            return $exit;
        }

        if ($withSeed) {
            $exit = Artisan::call('db:seed', ['--force' => true], $this->output);

            if ($exit !== 0) {
                return $exit;
            }
        }

        $this->info('Migration completed successfully.');

        return self::SUCCESS;
    }
}
