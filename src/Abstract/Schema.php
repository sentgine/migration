<?php

namespace Sentgine\Migration\Abstract;

use Closure;
use PDO;

/**
 * Abstract class Schema
 * Represents a database schema for performing migration operations.
 */
abstract class Schema
{
    /** @var PDO The PDO instance used for database operations. */
    protected PDO $pdo;

    /** @var string $defaultCollation The default collation for columns. */
    protected string $collation = 'utf8mb4_unicode_ci';

    /** @var string The character set for database connections. */
    protected string $charset = 'utf8mb4';

    /** @var string The storage engine for MySQL tables. */
    protected string $engine = 'InnoDB';

    /** @var string The comment for the table. */
    protected string $comment = "";

    /** @var string The MySQL driver constant. */
    public const DRIVER_MYSQL = 'mysql';

    /** @var string The PostgreSQL driver constant. */
    public const DRIVER_POSTGRES = 'pgsql';

    /** @var string The SQLite driver constant. */
    public const DRIVER_SQLITE = 'sqlite';

    /** @var string The SQL Server driver constant. */
    public const DRIVER_SQLSERVER = 'sqlsrv';

    /** @var string The Oracle driver constant. */
    public const DRIVER_ORACLE = 'oci';

    /** @var string Represents the 'create' method. */
    public const METHOD_CREATE = 'create';

    /** @var string Represents the 'update' method. */
    public const METHOD_UPDATE = 'update';

    /** @var string Represents the 'delete' method. */
    public const METHOD_DELETE = 'delete';

    /**
     * Constructor.
     *
     * @param string $host The database host.
     * @param string $database The database name.
     * @param string $username The database username.
     * @param string $password The database password.
     * @param string $driver The database driver (default is 'mysql').
     */
    public function __construct(string $host, string $database, string $username, string $password, string $driver = 'mysql')
    {
        $dsn = "$driver:host=$host;dbname=$database";
        $this->pdo = new PDO($dsn, $username, $password);
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    /**
     * Truncate a table in the database.
     *
     * @param string $tableName The name of the table to be truncated.
     * @return void
     */
    public function truncate(string $tableName): void
    {
        $sql = "TRUNCATE TABLE `$tableName`;";
        $this->pdo->exec($sql);
    }

    /**
     * Drop a table from the database.
     *
     * @param string $tableName The name of the table to be dropped.
     * @return void
     */
    public function drop(string $tableName): void
    {
        $sql = "DROP TABLE IF EXISTS `$tableName`;";
        $this->pdo->exec($sql);
    }

    /**
     * Drop a foreign key constraint from a table.
     *
     * @param string $tableName The name of the table.
     * @param string $constraintName The name of the foreign key constraint to drop.
     * @return void
     */
    public function dropForeignKey(string $tableName, string $constraintName): void
    {
        $sql = "ALTER TABLE `$tableName` DROP FOREIGN KEY `$constraintName`;";
        $this->pdo->exec($sql);
    }

    /**
     * Execute a raw SQL query on the database.
     *
     * @param string $sql The SQL query to execute.
     * @param array $bindings The parameter bindings for the query.
     * @return \PDOStatement The PDOStatement object representing the result of the query.
     */
    public function query(string $sql, array $bindings = []): \PDOStatement
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($bindings);
        return $stmt;
    }

    /**
     * Set the default collation for columns.
     *
     * @param string $collation The collation to set as default.
     * @return self
     */
    public function defaultCollation(string $collation): self
    {
        $this->collation = $collation;

        // Extract the charset from the collation
        $this->charset = explode('_', $this->collation)[0];

        return $this;
    }

    /**
     * Add a comment to the table.
     *
     * @param string $comment The comment to add.
     * @return self
     */
    public function comment(string $comment): self
    {
        $this->comment = $comment;
        return $this;
    }

    /**
     * Insert data into the specified table.
     *
     * @param string $tableName The name of the table to insert data into.
     * @param array $data An associative array where keys are column names and values are column values.
     * @return void
     */
    public abstract function insert(string $tableName, array $data): void;

    /**
     * Abstract method to create a new table in the database.
     *
     * @param string $tableName The name of the table to be created.
     * @param Closure $callback A callback function defining the table structure.
     * @return void
     */
    public abstract function create(string $tableName, Closure $callback): void;

    /**
     * Define an abstract method to alter a table's structure using a specified method and a closure.
     *
     * @param string $tableName The name of the table to alter.
     * @param Closure $callback The callback containing the alterations.
     * @return void
     */
    public abstract function alter(string $tableName, Closure $callback): void;
}
