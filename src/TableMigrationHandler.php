<?php

namespace Sentgine\Migration;

/**
 * Class TableMigrationHandler
 *
 * Handles table migrations with up and down methods.
 */
abstract class TableMigrationHandler
{
    /**
     * Abstract method for the "up" migration.
     *
     * @return void
     */
    abstract public function up(): void;

    /**
     * Abstract method for the "down" migration.
     *
     * @return void
     */
    abstract public function down(): void;
}
