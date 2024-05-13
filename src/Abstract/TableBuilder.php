<?php

namespace Sentgine\Migration\Abstract;

/**
 * Abstract class TableBuilder
 * Represents a builder for generating SQL statements for table creation.
 */
abstract class TableBuilder
{
    /** @var array $columns The columns to be added to the table. */
    public array $columns = [];

    /** @var array The names of columns in the table. */
    public array $columnNames = [];

    /** @var array $options Array of available column options. */
    protected array $options = ['NULL', 'NOT NULL', 'DEFAULT', 'COMMENT', 'UNSIGNED', 'UNIQUE', 'AFTER'];

    /** @var string $collation The default collation for columns. */
    protected string $collation;

    /** @var string The SQL query string. */
    protected string $sql;

    /** @var string The string representation for NULL values. */
    const NULL = 'NULL';

    /** @var string The string representation for the CURRENT_TIMESTAMP function. */
    const CURRENT_TIMESTAMP = 'CURRENT_TIMESTAMP';

    /**
     * Constructor.
     *
     * @param string &$sql A reference to the SQL statement being constructed.
     */
    public function __construct(string &$sql, string $collation = 'utf8mb4_unicode_ci')
    {
        $this->collation = $collation;
        $this->sql = &$sql;
    }

    /**
     * Set the default collation for columns.
     *
     * @param string $collation The collation to set as default.
     * @return self
     */
    abstract public function defaultCollation(string $collation): self;

    /**
     * Specify the default value for the column.
     *
     * @param mixed $value The default value. Use null, MySQLTableBuilder::NULL, or MySQLTableBuilder::CURRENT_TIMESTAMP.
     * @return self
     */
    abstract public function default(mixed $value = null): self;

    /**
     * Add a comment to the column.
     *
     * @param string $comment The comment to add.
     * @return self
     */
    abstract public function comments(string $comment): self;

    /**
     * Specify that the column can be NULL.
     *
     * @return self
     */
    abstract public function nullable(): self;

    /**
     * Specify that the column cannot be NULL.
     *
     * @return self
     */
    abstract public function required(): self;

    /**
     * Specify that the column is unsigned.
     *
     * @return self
     */
    abstract public function unsigned(): self;

    /**
     * Specify that the column must be unique.
     *
     * @return self
     */
    abstract public function unique(): self;

    /**
     * Specify an auto-incrementing integer (primary key) column.
     *
     * @param string $column The column name.
     * @return self
     */
    abstract public function increments(string $column): self;

    /**
     * Specify a big auto-incrementing integer (primary key) column.
     *
     * @param string $column The column name.
     * @return self
     */
    abstract public function bigIncrements(string $column): self;

    /**
     * Specify an integer column.
     *
     * @param string $column The column name.
     * @return self
     */
    abstract public function integer(string $column): self;

    /**
     * Specify a big integer column.
     *
     * @param string $column The column name.
     * @return self
     */
    abstract public function bigInteger(string $column): self;

    /**
     * Specify a decimal column.
     *
     * @param string $column The column name.
     * @param int $precision The precision of the column.
     * @param int $scale The scale of the column.
     * @return self
     */
    abstract public function decimal(string $column, int $precision = 8, int $scale = 2): self;

    /**
     * Specify a boolean (TINYINT) column.
     *
     * @param string $column The column name.
     * @return self
     */
    abstract public function boolean(string $column): self;

    /**
     * Specify a date column.
     *
     * @param string $column The column name.
     * @param string|null $collation The collation for the column.
     * @return self
     */
    abstract public function date(string $column, string $collation = null): self;

    /**
     * Specify a time column.
     *
     * @param string $column The column name.
     * @return self
     */
    abstract public function time(string $column): self;

    /**
     * Specify a datetime column.
     *
     * @param string $column The column name.
     * @return self
     */
    abstract public function dateTime(string $column): self;

    /**
     * Specify a timestamp column.
     *
     * @param string $column The column name.
     * @return self
     */
    abstract public function timestamp(string $column): self;

    /**
     * Specify a string (VARCHAR) column.
     *
     * @param string $column The column name.
     * @param int $length The length of the column.
     * @param string|null $collation The collation for the column.
     * @return self
     */
    abstract public function string(string $column, int $length = 255, string $collation = null): self;

    /**
     * Specify a text column.
     *
     * @param string $column The column name.
     * @return self
     */
    abstract public function text(string $column): self;

    /**
     * Specify a long text column.
     *
     * @param string $column The column name.
     * @return self
     */
    abstract public function longText(string $column): self;

    /**
     * Specify a UUID (CHAR) column.
     *
     * @param string $column The column name.
     * @param string|null $collation The collation for the column.
     * @return self
     */
    abstract public function uuid(string $column, string $collation = null): self;

    /**
     * Specify a BLOB column.
     *
     * @param string $column The column name.
     * @return self
     */
    abstract public function blob(string $column): self;

    /**
     * Specify an ENUM column.
     *
     * @param string $column The column name.
     * @param array $allowedValues The allowed values for the ENUM column.
     * @param string|null $collation The collation for the column.
     * @return self
     */
    abstract public function enum(string $column, array $allowedValues, string $collation = null): self;

    /**
     * Specify a SET column.
     *
     * @param string $column The column name.
     * @param array $allowedValues The allowed values for the SET column.
     * @param string|null $collation The collation for the column.
     * @return self
     */
    abstract public function set(string $column, array $allowedValues, string $collation = null): self;

    /**
     * Specify a JSON column.
     *
     * @param string $column The column name.
     * @param string|null $collation The collation for the column.
     * @return self
     */
    abstract public function json(string $column, string $collation = null): self;

    /**
     * Specify a BIT column.
     *
     * @param string $column The column name.
     * @param int $length The length of the column.
     * @return self
     */
    abstract public function bit(string $column, int $length): self;

    /**
     * Specify a GEOMETRY column.
     *
     * @param string $column The column name.
     * @return self
     */
    abstract public function geometry(string $column): self;

    /**
     * Specify a POINT column.
     *
     * @param string $column The column name.
     * @return self
     */
    abstract public function point(string $column): self;

    /**
     * Specify a LINESTRING column.
     *
     * @param string $column The column name.
     * @return self
     */
    abstract public function linestring(string $column): self;

    /**
     * Specify a POLYGON column.
     *
     * @param string $column The column name.
     * @return self
     */
    abstract public function polygon(string $column): self;

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
    abstract public function foreign(string $localColumn, string $foreignColumn, string $foreignTable, string $constraintName, bool $cascadeOnDelete = false): self;
}
