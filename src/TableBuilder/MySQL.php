<?php

namespace Sentgine\Migration\TableBuilder;

use Sentgine\Migration\Abstract\TableBuilder;
use Sentgine\Migration\Facades\Schema;

/**
 * Class MySQL Table Builder
 *
 * Represents the table builder for constructing SQL queries.
 */
class MySQL extends TableBuilder
{
    /** @var string Represents the alteration mode (e.g., 'add', 'change', 'drop'). */
    public string $alterMode = '';

    /**
     * Set the alteration mode.
     *
     * @param string|null $value The new alteration mode.
     * @return $this
     */
    public function setAlterMode($value)
    {
        $this->alterMode = $value ?? '';
        return $this;
    }

    /**
     * Set the alteration mode to add columns.
     *
     * @return $this
     */
    public function addColumns()
    {
        $this->setAlterMode(Schema::ALTER_ADD);
        return $this;
    }

    /**
     * Set the alteration mode to modify columns.
     *
     * @return $this
     */
    public function modifyColumns()
    {
        $this->setAlterMode(Schema::ALTER_CHANGE);
        return $this;
    }

    /**
     * Set the alteration mode to drop columns.
     *
     * @return $this
     */
    public function dropColumns()
    {
        $this->setAlterMode(Schema::ALTER_DROP);
        return $this;
    }

    /**
     * Determine the SQL alteration command based on the alteration mode.
     *
     * @return string The SQL alteration command.
     */
    private function alterColumn(): string
    {
        switch ($this->alterMode) {
            case Schema::ALTER_ADD:
                return 'ADD COLUMN ';
            case Schema::ALTER_CHANGE:
                return 'MODIFY COLUMN ';
            case Schema::ALTER_DROP:
                return 'DROP COLUMN ';
            default:
                return '';
        }
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
        return $this;
    }

    /**
     * Specify the default value for the column.
     *
     * @param mixed $value The default value. Use null, MySQLTableBuilder::NULL, or MySQLTableBuilder::CURRENT_TIMESTAMP.
     * @return self
     */
    public function default(mixed $value = null): self
    {
        if ($value !== null && !in_array($value, [self::NULL, self::CURRENT_TIMESTAMP])) {
            $defaultValue = " '$value'";
        }

        if (in_array($value, [null, self::NULL, self::CURRENT_TIMESTAMP])) {
            $defaultValue = ' ' . $value;
        }

        $this->columns[] = "DEFAULT" . $defaultValue;
        return $this;
    }

    /**
     * Add a comment to the column.
     *
     * @param string $comment The comment to add.
     * @return self
     */
    public function comments(string $comment): self
    {
        $this->columns[] = "COMMENT '$comment'";
        return $this;
    }

    /**
     * Specify that the column can be NULL.
     *
     * @return self
     */
    public function nullable(): self
    {
        $this->columns[] = 'NULL';
        return $this;
    }

    /**
     * Specify that the column cannot be NULL.
     *
     * @return self
     */
    public function required(): self
    {
        $this->columns[] = 'NOT NULL';
        return $this;
    }

    /**
     * Specify that the column is unsigned.
     *
     * @return self
     */
    public function unsigned(): self
    {
        $this->columns[] = 'UNSIGNED';
        return $this;
    }

    /**
     * Specify that the column must be unique.
     *
     * @return self
     */
    public function unique(): self
    {
        $column = end($this->columnNames);
        $index = $column;
        $this->columns[] = ", UNIQUE `$index` (`$column`)";
        return $this;
    }

    /**
     * Specify that the new column should be added after a specific existing column.
     *
     * @param string $column The name of the existing column.
     * @return self
     */
    public function after(string $column): self
    {
        $this->columns[] = "AFTER `$column`";
        return $this;
    }

    /**
     * Get the SQL statement for the columns.
     *
     * @return string The SQL statement.
     */
    public function getSQL(): string
    {
        $sql = "";
        $totalColumns = count($this->columns);

        foreach ($this->columns as $key => $column) {
            $sql .= ' ' . $column;

            if ($key < $totalColumns - 1 && !preg_match('/\b(?:' . implode('|', $this->options) . ')\b/', $this->columns[$key + 1])) {
                $sql .= ',';
            }
        }

        return trim($sql);
    }


    /**
     * Specify an auto-incrementing integer (primary key) column.
     *
     * @param string $column The column name.
     * @return self
     */
    public function increments(string $column): self
    {
        $this->columnNames[] = $column;
        $this->columns[] = "`$column` INT AUTO_INCREMENT PRIMARY KEY";
        return $this;
    }

    /**
     * Specify a big auto-incrementing integer (primary key) column.
     *
     * @param string $column The column name.
     * @return self
     */
    public function bigIncrements(string $column): self
    {
        $this->columnNames[] = $column;
        $this->columns[] = "`$column` BIGINT AUTO_INCREMENT PRIMARY KEY";
        return $this;
    }

    /**
     * Specify that a column should be dropped from the table.
     *
     * @param string $column The name of the column to drop.
     * @return self
     */
    public function drop(string $column): self
    {
        $this->columnNames[] = $column;
        $this->columns[] = "DROP COLUMN IF EXISTS `$column`";
        return $this;
    }

    /**
     * Specify an integer column.
     *
     * @param string $column The column name.
     * @return self
     */
    public function integer(string $column): self
    {
        $this->columnNames[] = $column;
        $this->columns[] = $this->alterColumn() . "`$column` INT";
        return $this;
    }

    /**
     * Specify a big integer column.
     *
     * @param string $column The column name.
     * @return self
     */
    public function bigInteger(string $column): self
    {
        $this->columnNames[] = $column;
        $this->columns[] = $this->alterColumn() . "`$column` BIGINT";
        return $this;
    }

    /**
     * Specify a decimal column.
     *
     * @param string $column The column name.
     * @param int $precision The precision of the column.
     * @param int $scale The scale of the column.
     * @return self
     */
    public function decimal(string $column, int $precision = 8, int $scale = 2): self
    {
        $this->columnNames[] = $column;
        $this->columns[] = $this->alterColumn() . "`$column` DECIMAL($precision, $scale)";
        return $this;
    }

    /**
     * Specify a boolean (TINYINT) column.
     *
     * @param string $column The column name.
     * @return self
     */
    public function boolean(string $column): self
    {
        $this->columnNames[] = $column;
        $this->columns[] = $this->alterColumn() . "`$column` TINYINT(1)";
        return $this;
    }

    /**
     * Specify a date column.
     *
     * @param string $column The column name.
     * @param string|null $collation The collation for the column.
     * @return self
     */
    public function date(string $column, string $collation = null): self
    {
        $this->columnNames[] = $column;
        $this->columns[] = $this->alterColumn() . "`$column` DATE";
        return $this;
    }

    /**
     * Specify a time column.
     *
     * @param string $column The column name.
     * @return self
     */
    public function time(string $column): self
    {
        $this->columnNames[] = $column;
        $this->columns[] = $this->alterColumn() . "`$column` TIME";
        return $this;
    }

    /**
     * Specify a datetime column.
     *
     * @param string $column The column name.
     * @return self
     */
    public function dateTime(string $column): self
    {
        $this->columnNames[] = $column;
        $this->columns[] = $this->alterColumn() . "`$column` DATETIME";
        return $this;
    }

    /**
     * Specify a timestamp column.
     *
     * @param string $column The column name.
     * @return self
     */
    public function timestamp(string $column): self
    {
        $this->columnNames[] = $column;
        $this->columns[] = $this->alterColumn() . "`$column` TIMESTAMP";
        return $this;
    }

    /**
     * Specify a string (VARCHAR) column.
     *
     * @param string $column The column name.
     * @param int $length The length of the column.
     * @param string|null $collation The collation for the column.
     * @return self
     */
    public function string(string $column, int $length = 255, string $collation = null): self
    {
        $this->columnNames[] = $column;
        $collation = $collation ?? $this->collation;
        $this->columns[] = $this->alterColumn() . "`$column` VARCHAR($length) COLLATE $collation";
        return $this;
    }

    /**
     * Specify a text column.
     *
     * @param string $column The column name.
     * @return self
     */
    public function text(string $column): self
    {
        $this->columnNames[] = $column;
        $collation = $this->collation;
        $this->columns[] = $this->alterColumn() . "`$column` TEXT COLLATE $collation";
        return $this;
    }

    /**
     * Specify a long text column.
     *
     * @param string $column The column name.
     * @return self
     */
    public function longText(string $column): self
    {
        $collation = $this->collation;
        $this->columns[] = $this->alterColumn() . "`$column` LONGTEXT COLLATE $collation";
        return $this;
    }

    /**
     * Specify a UUID (CHAR) column.
     *
     * @param string $column The column name.
     * @param string|null $collation The collation for the column.
     * @return self
     */
    public function uuid(string $column, string $collation = null): self
    {
        $this->columnNames[] = $column;
        $collation = $collation ?? $this->collation;
        $this->columns[] = $this->alterColumn() . "`$column` CHAR(36) COLLATE $collation";
        return $this;
    }

    /**
     * Specify a BLOB column.
     *
     * @param string $column The column name.
     * @return self
     */
    public function blob(string $column): self
    {
        $this->columnNames[] = $column;
        $this->columns[] = $this->alterColumn() . "`$column` BLOB";
        return $this;
    }

    /**
     * Specify an ENUM column.
     *
     * @param string $column The column name.
     * @param array $allowedValues The allowed values for the ENUM column.
     * @param string|null $collation The collation for the column.
     * @return self
     */
    public function enum(string $column, array $allowedValues, string $collation = null): self
    {
        $this->columnNames[] = $column;
        $collation = $collation ?? $this->collation;
        $enumValues = "'" . implode("', '", $allowedValues) . "'";
        $this->columns[] = $this->alterColumn() . "`$column` ENUM($enumValues) COLLATE $collation";
        return $this;
    }

    /**
     * Specify a SET column.
     *
     * @param string $column The column name.
     * @param array $allowedValues The allowed values for the SET column.
     * @param string|null $collation The collation for the column.
     * @return self
     */
    public function set(string $column, array $allowedValues, string $collation = null): self
    {
        $this->columnNames[] = $column;
        $setValues = "'" . implode("', '", $allowedValues) . "'";
        $this->columns[] = $this->alterColumn() . "`$column` SET($setValues)";
        return $this;
    }

    /**
     * Specify a JSON column.
     *
     * @param string $column The column name.
     * @param string|null $collation The collation for the column.
     * @return self
     */
    public function json(string $column, string $collation = null): self
    {
        $this->columnNames[] = $column;
        $this->columns[] = $this->alterColumn() . "`$column` JSON";
        return $this;
    }

    /**
     * Specify a BIT column.
     *
     * @param string $column The column name.
     * @param int $length The length of the column.
     * @return self
     */
    public function bit(string $column, int $length): self
    {
        $this->columnNames[] = $column;
        $this->columns[] = $this->alterColumn() . "`$column` BIT($length)";
        return $this;
    }

    /**
     * Specify a GEOMETRY column.
     *
     * @param string $column The column name.
     * @return self
     */
    public function geometry(string $column): self
    {
        $this->columnNames[] = $column;
        $this->columns[] = $this->alterColumn() . "`$column` GEOMETRY";
        return $this;
    }

    /**
     * Specify a POINT column.
     *
     * @param string $column The column name.
     * @return self
     */
    public function point(string $column): self
    {
        $this->columnNames[] = $column;
        $this->columns[] = $this->alterColumn() . "`$column` POINT";
        return $this;
    }

    /**
     * Specify a LINESTRING column.
     *
     * @param string $column The column name.
     * @return self
     */
    public function linestring(string $column): self
    {
        $this->columnNames[] = $column;
        $this->columns[] = $this->alterColumn() . "`$column` LINESTRING";
        return $this;
    }

    /**
     * Specify a POLYGON column.
     *
     * @param string $column The column name.
     * @return self
     */
    public function polygon(string $column): self
    {
        $this->columnNames[] = $column;
        $this->columns[] = $this->alterColumn() . "`$column` POLYGON";
        return $this;
    }

    /**
     * Define a foreign key constraint.
     *
     * @param string $localColumn The name of the local column.
     * @param string $foreignColumn The name of the foreign column.
     * @param string $foreignTable The name of the foreign table.
     * @param string $constraintName The name of the constraint.
     * @param bool $cascadeOnDelete Whether to cascade on delete.
     * @return $this
     */
    public function foreign(string $localColumn, string $foreignColumn, string $foreignTable, string $constraintName, bool $cascadeOnDelete = false): self
    {
        $constraint = '';

        // Build the foreign key constraint statement
        if ($this->alterMode == Schema::ALTER_ADD) {
            $constraint .= "ADD ";
        }

        $constraint .= "CONSTRAINT `$constraintName`";
        $constraint .= " FOREIGN KEY (`$localColumn`)";
        $constraint .= " REFERENCES `$foreignTable` (`$foreignColumn`)";

        // Add ON DELETE CASCADE option if requested
        if ($cascadeOnDelete) {
            $constraint .= " ON DELETE CASCADE";
        }

        // Add the constraint to the list of table columns
        $this->columns[] = $constraint;

        // Return the instance of the table builder for method chaining
        return $this;
    }
}
