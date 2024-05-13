<?php

namespace Sentgine\Migration\Schema;

use Closure;
use Sentgine\Migration\Abstract\Schema;
use Sentgine\Migration\TableBuilder\SQLite;

/**
 * Class SQLiteSchema
 *
 * Represents the SQLite implementation of the Schema class.
 */
class SQLiteSchema extends Schema
{
    /**
     * Create a new table.
     *
     * @param string $tableName The name of the table to create.
     * @param Closure $callback The closure defining the table schema.
     * @return void
     */
    public function create(string $tableName, Closure $callback): void
    {
        $sql = "CREATE TABLE $tableName ("; // Start creating the SQL query
        $builder = new SQLite($sql, $this->collation); // Initialize a new Blueprint instance
        $callback($builder); // Execute the callback with the builder instance   
        $sql .= $builder->getSQL(); // Append the generated SQL from the builder
        $sql .= ")"; // Add closing parenthesis
        $sql .= " ENGINE=$this->engine"; // Add engine if it is not set
        $sql .= " CHARSET=$this->charset"; // Add charset if it is not set
        $sql .= " COLLATE $this->collation"; // Add collation if it is not set

        // Add comment if it is not set
        if ($this->comment !== '') {
            $sql .= " COMMENT='$this->comment'";
        }

        $this->pdo->exec($sql); // Execute the SQL query
    }

    /**
     * Insert data into the specified table.
     *
     * @param string $tableName The name of the table to insert data into.
     * @param array $data An associative array where keys are column names and values are column values.
     * @return void
     */
    public function insert(string $tableName, array $data): void
    {
    }

    /**
     * Alter a table in the database schema using the specified method and closure.
     *
     * @param string $tableName The name of the table to alter.
     * @param Closure $callback The closure containing the alteration logic.
     * @return void
     */
    public function alter(string $tableName, Closure $callback): void
    {
    }
}
