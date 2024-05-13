<?php

namespace Sentgine\Migration\Commands;

use Sentgine\Arise\Command;
use Sentgine\Helper\Word;
use Sentgine\Migration\Abstract\Schema as AbstractSchema;
use Sentgine\Migration\Facades\Schema;
use Sentgine\Migration\Schema\MySQLSchema;
use Sentgine\Migration\Schema\PostgreSQLSchema;
use Sentgine\Migration\Schema\SQLiteSchema;

class Migrate extends Command
{
    protected string $signature = 'migrate';
    protected string $description = 'Run the database migrations';

    /**
     * Configures the command.
     */
    protected function configure(): void
    {
        parent::configure();
    }

    /**
     * Handles the execution of the command.
     *
     * @return int The exit code
     */
    protected function handle(): int
    {
        $directory = getcwd() . '/database/migrations';
        $migrations = glob($directory . '/*.php');

        $dbConfig = Schema::getConfig();
        $credentials = $dbConfig['credentials'];
        $driver = $credentials['driver'];
        $schema = null;

        switch ($driver) {
            case AbstractSchema::DRIVER_MYSQL:
                $migrations = array_merge([
                    __DIR__ . '/../Migrations/2024_04_21_090712_create_migrations_table_mysql.php'
                ], $migrations);
                $schema = new MySQLSchema(...$credentials);
                break;
            case AbstractSchema::DRIVER_POSTGRES:
                $migrations = array_merge([
                    __DIR__ . '/../Migrations/2024_04_21_090712_create_migrations_table_postgres.php'
                ], $migrations);
                $schema = new PostgreSQLSchema(...$credentials);
                break;
            case AbstractSchema::DRIVER_SQLITE:
                $migrations = array_merge([
                    __DIR__ . '/../Migrations/2024_04_21_090712_create_migrations_table_sqllite.php'
                ], $migrations);
                $schema = new SQLiteSchema(...$credentials);
                break;
            default:
                throw new \Exception("Unsupported database driver: {$credentials['driver']}");
        }

        // Set initial batch number to 1 if it doesn't exist yet
        $currentBatch = 1;

        // Check if the migrations table exists
        $migrationsTableExists = $schema->query("SHOW TABLES LIKE 'migrations'")->rowCount() > 0;

        // If the migrations table exists, proceed with retrieving the current batch number
        $currentlyMigrated = [];
        if ($migrationsTableExists) {
            // Get the current maximum batch number from the migrations table
            $currentBatch = $schema->query('SELECT MAX(batch) AS max_batch FROM migrations')->fetch()['max_batch'];

            // If there are no previous migrations, set the current batch to 0, otherwise increment it
            $currentBatch = $currentBatch !== null ? $currentBatch + 1 : 1;

            // Get list of migrations
            $currentlyMigrated = $schema->query('SELECT migration FROM migrations')->fetchAll();
        }
        $migrationPerformed = false;

        // List all of the migrated files in the database
        $migrated = [];
        foreach ($currentlyMigrated as $value) {
            $migrated[] = $value['migration'];
        }

        foreach ($migrations as $key => $migration) {
            $segment = explode('/', $migration);
            $filename = Word::of(end($segment))->replace('.php', '')->get();

            // Only run migrations that are not yet ran
            if (!in_array($filename, $migrated)) {
                // Instantiate class
                $class = require_once $migration;
                $class->up();
            }

            // Check if it's the first iteration
            if ($key !== 0) {
                try {
                    // Insert only if it's not the first iteration
                    $schema->insert('migrations', [
                        'migration' => $filename,
                        'batch' => $currentBatch,
                    ]);
                    $migrationPerformed = true; // Set flag to true indicating a migration was performed
                    $this->info("Migrated: ✔ {$filename}");
                } catch (\PDOException $e) {
                    // Check if the error code indicates a duplicate entry error
                    if ($e->getCode() == '23000') {
                        // Duplicate entry error, ignore and continue
                        // This will be skipped as the migration already exists
                    } else {
                        // Other PDOExceptions, rethrow the exception
                        throw $e;
                    }
                }
            }
        }

        // Output "Nothing to migrate" if no migrations were performed
        if (!$migrationPerformed) {
            $this->comment("✘ Nothing to migrate!");
            return true;
        }

        return true;
    }
}
