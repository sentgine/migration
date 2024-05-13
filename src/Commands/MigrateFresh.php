<?php

namespace Sentgine\Migration\Commands;

use PDOException;
use Sentgine\Arise\Command;
use Sentgine\Helper\Word;
use Sentgine\Migration\Facades\Schema;

class MigrateFresh extends Command
{
    protected string $signature = 'migrate:fresh';
    protected string $description = 'Drop all tables and re-run all migrations';

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
        $schema = Schema::getSchema();
        $migrations = [];
        $migrationPerformed = false;

        try {
            $query = "SELECT * FROM migrations";
            $result = $schema->query($query)->fetchAll();
            if (count($result) > 0) {
                foreach ($result as $key => $value) {
                    $migrations[$key]['migration'] = $value['migration'];
                    $migrations[$key]['batch'] = $value['batch'];
                    $this->call('migrate:rollback');
                }


                foreach ($migrations as $key => $value) {
                    $filename = Word::of($value['migration'])->append('.php')->get();

                    // Instantiate class
                    $class = require getcwd() . '/database/migrations/' . $filename;
                    $class->up();

                    try {
                        $schema->insert('migrations', [
                            'migration' => $value['migration'],
                            'batch' => $value['batch'],
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
        } catch (PDOException $e) {
            // Check if the error message or error code matches the expected error
            if ($e->getCode() == '42S02' || strpos($e->getMessage(), "Table 'test2.migrations' doesn't exist") !== false) {
                $this->comment('Migration not started. Migrating...');
                $this->call('migrate');
            } else {
                // For other errors, rethrow the exception or handle them accordingly
                throw $e;
            }
        }

        // Output "Nothing to migrate" if no migrations were performed
        if (!$migrationPerformed) {
            $this->comment('Migration not started. Migrating...');
            $this->call('migrate');
            return true;
        }

        $this->comment("Freshly migrated: ✔ All migrations");

        return true;
    }
}
