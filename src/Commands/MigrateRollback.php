<?php

namespace Sentgine\Migration\Commands;

use Sentgine\Arise\Command;
use Sentgine\Migration\Facades\Schema;

class MigrateRollback extends Command
{
    protected string $signature = 'migrate:rollback';
    protected string $description = 'Rollback the last database migration';

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

        // Check if the migrations table exists
        $migrationsTableExists = $schema->query("SHOW TABLES LIKE 'migrations'")->rowCount() > 0;

        if ($migrationsTableExists) {
            // Get the current maximum batch number from the migrations table
            $currentBatch = $schema->query('SELECT MAX(batch) AS max_batch FROM migrations')->fetch()['max_batch'];

            // Retrieve all records with the highest batch number
            $query = "SELECT * FROM migrations WHERE batch = :batch";
            $bindings = [':batch' => $currentBatch];
            $result = $schema->query($query, $bindings)->fetchAll();

            // Process the result (e.g., display or perform rollback actions)
            foreach ($result as $record) {
                $migration = $record['migration'];
                $class = require getcwd() . '/database/migrations/' . $migration . '.php';
                $class->down();

                // Disable foreign key checks
                $schema->query('SET FOREIGN_KEY_CHECKS = 0');

                // Execute your DELETE statement
                $schema->query("DELETE FROM migrations WHERE migration = '{$migration}'");

                // Re-enable foreign key checks
                $schema->query('SET FOREIGN_KEY_CHECKS = 1');

                // Log the rollback
                $this->info("Rollback migration: ✔ {$migration}");
            }
        } else {
            $this->comment("✘ No migrations table found. Nothing to rollback.");
        }

        return true;
    }
}
