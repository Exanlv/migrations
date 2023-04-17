# Migrations

A very simple framework agnostic migrations package.

```
composer require exan/migrations
```

## How it works

For the migrations, you provide an `up.php` and an optional `down.php`. When running the migrations, all `up.php` scripts will be run if they have not previously ran.

After running a migration, a `.migrated` file will be created. You should add these to your .gitignore `your-migration-dir/*/.migrated`.

## Creating a migration

You can create a migration using `./vendor/exan/migrations/bin/migrate create your-migration-dir your-migration-name`.

This will create a migration called `(creation date)_your-migration-name` in the directory `your-migration-dir`. You can then modify `up.php` and `down.php`. You can also choose to delete `down.php` if preferred.

## Running migrations

You can run the migrations using `./vendor/exan/migrations/bin/migrate migrate` and roll them back with `./vendor/exan/migrations/bin/migrate rollback`
