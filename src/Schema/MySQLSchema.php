<?php

namespace Sentgine\Migration\Schema;

use Closure;
use Sentgine\Migration\Abstract\Schema;
use Sentgine\Migration\TableBuilder\MySQL;

/**
 * Class MySQLSchema
 * Represents a MySQL schema for creating tables.
 */
class MySQLSchema extends Schema
{
    /**
     * Create a new table in the database.
     *
     * @param string $tableName The name of the table to be created.
     * @param Closure $callback A callback function defining the table structure.
     * @return void
     */
    public function create(string $tableName, Closure $callback): void
    {
        $sql = "CREATE TABLE IF NOT EXISTS `$tableName` (";
        $builder = new MySQL($sql, $this->collation);
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
        // Build the SQL INSERT query
        $columns = implode(', ', array_keys($data));
        $values = implode(', ', array_fill(0, count($data), '?'));

        $sql = "INSERT INTO $tableName ($columns) VALUES ($values)";

        // Prepare the SQL statement
        $statement = $this->pdo->prepare($sql);

        // Bind parameters and execute the statement
        $i = 1;
        foreach ($data as $value) {
            $statement->bindValue($i++, $value);
        }

        $statement->execute();
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
        $sql = "ALTER TABLE `$tableName` ";
        $builder = new MySQL($sql, $this->collation);
        $callback($builder); // Execute the callback with the builder instance   
        $sql .= $builder->getSQL(); // Append the generated SQL from the builder
        $this->pdo->exec($sql); // Execute the SQL query
    }
}
