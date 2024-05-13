# Migration by Sentgine

[![License](https://img.shields.io/badge/license-MIT-blue.svg)](LICENSE.md)
[![Latest Stable Version](https://img.shields.io/packagist/v/sentgine/migration.svg)](https://packagist.org/sentgine/migration)
[![Total Downloads](https://img.shields.io/packagist/dt/sentgine/migration.svg)](https://packagist.org/packages/sentgine/migration)

This PHP tool represents a standalone migration script for creating and altering tables in a MySQL database.This migration script is designed to be used independently to manage database migrations. It utilizes the [Arise](https://github.com/sentgine/arise) command line tool to execute various migration-related tasks.

### Upcoming Support for PostGreSQL and SQLite:

**Note**: Support for PostGreSQL and SQLite is currently in development and will be available soon. Stay tuned for updates on these additional database platforms!

## Requirements
- PHP 8.1.17 or higher.

## Available Commands

- **migrate**: Run the database migrations.
- **make:command**: Create a new command.
- **make:migration**: Create a migration file.
- **migrate:fresh**: Drop all tables and re-run all migrations.
- **migrate:rollback**: Rollback the last database migration.


(1) You can install the package via Composer to your existing PHP project by running the following command:

```bash
composer require sentgine/migration:^1.0.0
```

(2) In the root directory of your project, please run:

```bash
./vendor/sentgine/migration/initialize
```
This will create the executable "arise" command on your project.

(3) In the root directory of your project, you can run:

```bash
php arise
```

This will display a list of available commands that you can use to manage your database migrations efficiently.

## Changelog
Please see the [CHANGELOG](https://github.com/sentgine/arise/blob/main/CHANGELOG.md) file for details on what has changed.

## Security
If you discover any security-related issues, please email sentgine@gmail.com instead of using the issue tracker.

## Credits
**Migration** is built and maintained by Adrian Navaja.
- Check out some cool tutorials and stuff on [YouTube](https://www.youtube.com/@sentgine)!
- Catch my latest tweets and updates on [Twitter](https://twitter.com/sentgine) (formerly X)!
- Let's connect on a more professional note over on [LinkedIn](https://www.linkedin.com/in/adrian-navaja/)!
- For more information about me and my work, visit my website: [sentgine.com](https://www.sentgine.com/).

## License
The MIT License (MIT). Please see the [LICENSE](https://github.com/sentgine/arise/blob/main/LICENSE) file for more information.