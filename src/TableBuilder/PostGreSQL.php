<?php

namespace Sentgine\Migration\TableBuilder;

use Sentgine\Migration\Abstract\TableBuilder;

/**
 * Class PostGreSQL Table Builder
 *
 * Represents the table builder for constructing SQL queries.
 */
class PostGreSQL extends TableBuilder
{
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
     * Specify an integer column.
     *
     * @param string $column The column name.
     * @return self
     */
    public function integer(string $column): self
    {
        $this->columnNames[] = $column;
        $this->columns[] = "`$column` INT";
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
        $this->columns[] = "`$column` BIGINT";
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
        $this->columns[] = "`$column` DECIMAL($precision, $scale)";
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
        $this->columns[] = "`$column` TINYINT(1)";
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
        $this->columns[] = "`$column` DATE";
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
        $this->columns[] = "`$column` TIME";
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
        $this->columns[] = "`$column` DATETIME";
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
        $this->columns[] = "`$column` TIMESTAMP";
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
        $this->columns[] = "`$column` VARCHAR($length) COLLATE $collation";
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
        $this->columns[] = "`$column` TEXT COLLATE $collation";
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
        $this->columns[] = "`$column` LONGTEXT COLLATE $collation";
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
        $this->columns[] = "`$column` CHAR(36) COLLATE $collation";
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
        $this->columns[] = "`$column` BLOB";
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
        $this->columns[] = "`$column` ENUM($enumValues) COLLATE $collation";
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
        $this->columns[] = "`$column` SET($setValues)";
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
        $this->columns[] = "`$column` JSON";
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
        $this->columns[] = "`$column` BIT($length)";
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
        $this->columns[] = "`$column` GEOMETRY";
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
        $this->columns[] = "`$column` POINT";
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
        $this->columns[] = "`$column` LINESTRING";
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
        $this->columns[] = "`$column` POLYGON";
        return $this;
    }
}
