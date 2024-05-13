<?php

use Sentgine\Migration\TableBuilder\MySQL;
use Sentgine\Migration\Facades\Schema;
use Sentgine\Migration\TableMigrationHandler;

return new class extends TableMigrationHandler
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('migrations', function (MySQL $table) {
            $table->increments('id');
            $table->string('migration')->required()->unique();
            $table->integer('batch')->required();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::drop('migrations');
    }
};
