<?php

use Sentgine\File\Filesystem;
use Sentgine\Migration\Commands\Migrate;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Sentgine\Migration\Commands\MigrateCreate;
use Sentgine\Migration\Commands\MigrateFresh;
use Sentgine\Migration\Commands\MigrateRollback;
use Sentgine\Migration\Facades\Schema;

test('php arise make:migration - creates the migration file', function () {
    // Arrange
    $application = new Application();
    $application->add(new MigrateCreate());
    $command = $application->find('make:migration');
    $commandTester = new CommandTester($command);

    // Set the desired migration name
    $commandTester->setInputs(['create_users_table']);
    $commandTester->execute(['command' => $command->getName()]);

    // Find the most recently created migration file
    $migrationFiles = glob(getcwd() . '/database/migrations/*.php');
    $latestMigrationFile = end($migrationFiles);

    // Assert
    $output = $commandTester->getDisplay();
    expect($output)->toContain('Migration file created successfully!');
    expect($latestMigrationFile)->toBeFile(); // Ensure the file exists
});

test('php arise migrate - runs the migration', function () {
    // Arrange
    $application = new Application();
    $application->add(new Migrate());

    $command = $application->find('migrate');
    $commandTester = new CommandTester($command);
    $commandTester->execute(['command' => $command->getName()]);

    // Assert
    $output = $commandTester->getDisplay();
    expect($output)->toContain('Migrated:');
});

test('php arise migrate:fresh - drops all migrations and reruns the migration files', function () {

    // Make migration
    $application = new Application();
    $application->add(new Migrate());
    $application->add(new MigrateCreate());
    $application->add(new MigrateRollback());
    $application->add(new MigrateFresh());

    // Run migrate:fresh
    $command = $application->find('migrate:fresh');
    $commandTester = new CommandTester($command);
    $commandTester->execute(['command' => $command->getName()]);

    // Assert
    $output = $commandTester->getDisplay();
    expect($output)->toContain('Freshly migrated:');
});

test('php arise migrate:rollback - rolls back the previous migration(s)', function () {
    // Arrange
    $application = new Application();
    $application->add(new MigrateRollback());

    // Run command
    $command = $application->find('migrate:rollback');
    $commandTester = new CommandTester($command);
    $commandTester->execute(['command' => $command->getName()]);

    // Assert
    $output = $commandTester->getDisplay();
    expect($output)->toContain('Rollback migration:');
    refresh();
});

function refresh()
{
    // Remove directory after test
    $filesystem = new Filesystem();
    $directory = getcwd() . '/database';
    $filesystem->removeDirectory($directory);
    Schema::drop('migration');
}
