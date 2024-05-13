<?php

namespace Sentgine\Migration\Commands;

use Sentgine\Arise\Command;
use Sentgine\File\Filesystem;
use Sentgine\Helper\Word;
use Sentgine\Migration\Abstract\Schema as AbstractSchema;
use Sentgine\Migration\Facades\Schema;

class MigrateCreate extends Command
{
    protected string $signature = 'make:migration';
    protected string $description = 'Creates a migration file';

    /**
     * Configures the command.
     */
    protected function configure(): void
    {
        parent::configure();
        $this->argument('name', 'The name of the migration file', '');
    }

    /**
     * Handles the execution of the command.
     *
     * @return int The exit code
     */
    protected function handle(): int
    {
        $filesystem = new Filesystem();

        // Create base directory if it doesn't exist
        $filesystem->createDirectory(getcwd() . '/database');
        $filesystem->createDirectory(getcwd() . '/database/config');
        $filesystem->createDirectory(getcwd() . '/database/migrations');

        // Create connection file
        if (!file_exists(getcwd() . '/database/config/connection.php')) {
            $filesystem->setSourceFile(__DIR__ . '/../Stubs/connection.stub')
                ->setDestinationFile(getcwd() . '/database/config/connection.php')
                ->copy();
        }

        $migrationFileName = $this->getArgument('name');

        do {
            try {
                if (empty($migrationFileName) || $migrationFileName === "") {
                    $migrationFileName = $this->question('Enter the name of the migration file', $migrationFileName);
                }

                if (!$this->validateTableName($migrationFileName)) {
                    $this->writeWithColor('Invalid migration name format:', 'red');
                    $this->writeWithColor('For example:', 'blue');
                    $this->printSection(content: [
                        '- create_users_table',
                        '- update_users_table',
                        '- delete_users_table',
                        '- update_users_table_add_field_name',
                        '- update_users_table_drop_field_name',
                        '- update_users_table_change_field_name',
                        '- update_users_table_change_many_fields',
                        '- update_users_table_add_foreign_key',
                    ]);
                    $migrationFileName = "";
                }
            } catch (\Exception $e) {
                $this->error('An error occurred: ' . $e->getMessage());
                $migrationFileName = ""; // Reset the variable to continue the loop                
            }
        } while (empty($migrationFileName) || $migrationFileName === "");

        $migrationFileName = Word::of($migrationFileName)->toLower();
        $segmentedFilename = explode('_', $migrationFileName); // e.g., create_users_table
        $methodName = $segmentedFilename[0] ?? ''; // Method name: create
        $subMethodName = '';
        $tableName = $segmentedFilename[1] ?? ''; // Table name: users

        $dbConfig = Schema::getConfig();
        $credentials = $dbConfig['credentials'];
        $driver = $credentials['driver'];

        switch ($driver) {
            case AbstractSchema::DRIVER_MYSQL:
                $tableBuilder = 'MySQL';
                break;
            case AbstractSchema::DRIVER_POSTGRES:
                $tableBuilder = 'PostGreSQL';
                break;
            case AbstractSchema::DRIVER_SQLITE:
                $tableBuilder = 'SQLlite';
                break;
            default:
                throw new \Exception("Unsupported database driver: {$credentials['driver']}");
        }

        $fileName = $this->generateTableName($migrationFileName);

        $replacements = [];
        switch ($methodName) {
            case AbstractSchema::METHOD_CREATE:
                $content = $filesystem->setSourceFile(__DIR__ . '/../Stubs/CreateMigration.stub')->read();
                $replacements = [
                    'class_name' => $tableBuilder,
                    'method_name' => $methodName,
                    'table_name' => $tableName,
                ];
                break;
            case AbstractSchema::METHOD_UPDATE:
                $content = $filesystem->setSourceFile(__DIR__ . '/../Stubs/UpdateMigration.stub')->read();
                $subMethodName = $segmentedFilename[3] ?? ''; // Sub-method: add, change, or remove

                // Set the sub-method name
                if (!in_array($subMethodName, ['add', 'change', 'drop'])) {
                    $subMethodName = 'add';
                }

                $replacements = [
                    'class_name' => $tableBuilder,
                    'method_name' => $methodName,
                    'sub_method_name' => $subMethodName,
                    'table_name' => $tableName,
                ];
                break;
            case AbstractSchema::METHOD_DELETE:
                $content = $filesystem->setSourceFile(__DIR__ . '/../Stubs/DeleteMigration.stub')->read();
                $subMethodName = $segmentedFilename[3] ?? ''; // Sub-method: add, change, or remove

                // Set the sub-method name
                if (!in_array($subMethodName, ['add', 'change', 'drop'])) {
                    $subMethodName = 'add';
                }

                $replacements = [
                    'class_name' => $tableBuilder,
                    'method_name' => $methodName,
                    'sub_method_name' => $subMethodName,
                    'table_name' => $tableName,
                ];
                break;
            default:
                throw new \Exception("Unsupported method name: {$methodName}");
                break;
        }

        // Set the path of the migration file
        $destinationFilePath = "/database/migrations/$fileName.php";
        $destinationFile = getcwd() . "/database/migrations/$fileName.php";

        // Create the migration file
        $filesystem->setDestinationFile($destinationFile)->create($content);

        // Replace the migration file content placeholders
        $filesystem->replaceContent($replacements);

        // Prompt the user that the file has been created
        $this->writeLine('');
        $this->info("✔ Migration file created successfully!");
        $this->comment("Path: $destinationFilePath");

        return true;
    }

    /**
     * Generate a timestamped table name.
     *
     * @param string $tableName The base name of the table.
     * @return string The generated table name.
     */
    private function generateTableName($tableName)
    {
        $timestamp = date('Y_m_d_His'); // Get the current timestamp
        $fileName = $timestamp . '_' . $tableName; // Concatenate timestamp and table name
        return $fileName; // Return the generated table name
    }

    /**
     * Validate a table name against a specified format.
     *
     * @param string $tableName The table name to validate.
     * @return bool True if the table name matches the format, false otherwise.
     */
    private function validateTableName($tableName): bool
    {
        // Define the regex pattern for the desired format
        $pattern = '/^(create|update|delete)_[a-z]+_table(?:_(add|change|remove)_[a-z]+)?(?:_[a-z]+)?/';

        // Check if the table name matches the pattern
        if (preg_match($pattern, $tableName)) {
            return true; // Valid format
        } else {
            return false; // Invalid format
        }
    }
}
