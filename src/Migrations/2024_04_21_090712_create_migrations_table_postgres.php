<?php

use Sentgine\Migration\TableBuilder\PostGreSQL;
use Sentgine\Migration\Facades\Schema;
use Sentgine\Migration\TableMigrationHandler;

return new class extends TableMigrationHandler
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('migrations', function (PostGreSQL $table) {
            $table->increments('id');
            $table->string('migration')->required();
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
