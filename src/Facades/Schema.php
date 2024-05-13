<?php

namespace Sentgine\Migration\Facades;

use Closure;
use Sentgine\Migration\Abstract\Schema as AbstractSchema;
use Sentgine\Migration\Schema\MySQLSchema;
use Sentgine\Migration\Schema\PostgreSQLSchema;
use Sentgine\Migration\Schema\SQLiteSchema;

/**
 * Class Schema
 *
 * Facade for interacting with database schema operations.
 */
class Schema
{
    /** @var string|null The database connection that should be used by the migration. */
    protected static ?string $connection = null;

    /** @var array The credentials for the database connection. */
    protected static array $credentials = [];

    /** @var AbstractSchema|null The schema object for database operations. */
    protected static ?AbstractSchema $schema = null;

    /** @var string Represents the 'ADD' method for table alteration. */
    public const ALTER_ADD = 'ADD';

    /** @var string Represents the 'CHANGE' method for table alteration. */
    public const ALTER_CHANGE = 'CHANGE';

    /** @var string Represents the 'DROP' method for table alteration. */
    public const ALTER_DROP = 'DROP';

    /**
     * Set the connection to be used.
     *
     * @param string $connection The name of the database connection.
     * @return void
     */
    public static function setConnection(string $connection): void
    {
        self::$connection = $connection;
    }

    /**
     * Get the database configuration.
     *
     * @param string $path The path to the configuration file.
     * @return array The database configuration.
     */
    public static function getConfig(string $path = '/database/config/connection.php'): array
    {
        $config = require getcwd() . $path; // Load the configuration file

        // Extract connection and credentials from the configuration
        return [
            'connection' => $config['defaultConnection'] ?? 'database1',
            'credentials' => $config['connections'][$config['defaultConnection']] ?? [],
        ];
    }

    /**
     * Retrieve the schema object for database operations.
     *
     * @return AbstractSchema The schema object.
     */
    public static function getSchema(): AbstractSchema
    {
        if (self::$schema === null) {
            $config = self::getConfig();
            $credentials = $config['credentials'];

            switch ($credentials['driver']) {
                case AbstractSchema::DRIVER_MYSQL:
                    self::$schema = new MySQLSchema(...$credentials);
                    break;
                case AbstractSchema::DRIVER_POSTGRES:
                    self::$schema = new PostgreSQLSchema(...$credentials);
                    break;
                case AbstractSchema::DRIVER_SQLITE:
                    self::$schema = new SQLiteSchema(...$credentials);
                    break;
                default:
                    throw new \Exception("Unsupported database driver: {$credentials['driver']}");
            }
        }

        return self::$schema;
    }

    /**
     * Create a new table.
     *
     * @param string $tableName The name of the table to create.
     * @param Closure $closure The closure defining the table schema.
     * @return void
     */
    public static function create(string $tableName, Closure $closure): void
    {
        $schema = self::getSchema();
        $schema->create($tableName, $closure);
    }

    /**
     * Perform alterations on a table in the database schema using a closure.
     *
     * @param string $tableName The name of the table to alter.
     * @param Closure $closure The closure containing the alterations.
     * @return void
     */
    public static function alter(string $tableName, Closure $closure): void
    {
        $schema = self::getSchema();
        $schema->alter($tableName, $closure);
    }

    /**
     * Drop an existing table.
     *
     * @param string $tableName The name of the table to drop.
     * @return void
     */
    public static function drop(string $tableName): void
    {
        $schema = self::getSchema();
        $schema->drop($tableName);
    }

    /**
     * Drop a foreign key constraint from a table.
     *
     * @param string $tableName The name of the table.
     * @param string $column The name of the column containing the foreign key constraint.
     * @return void
     */
    public static function dropForeignKey(string $tableName, string $column): void
    {
        $schema = self::getSchema();
        $schema->dropForeignKey($tableName, $column);
    }

    /**
     * Execute a raw SQL query.
     *
     * @param string $sql The SQL query to execute.
     * @return void
     */
    public static function rawSQL(string $sql): void
    {
        $schema = self::getSchema();
        $schema->query($sql);
    }
}
