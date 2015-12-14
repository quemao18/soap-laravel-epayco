## Lumen API Starter

### Setup

You will need to point your document root to the `public` folder. PHP 5.5 is required along with MySQL.


### Database

To set up the database, you will need to run the following commands in the root directory:

	php artisan migrate:install
	php artisan migrate:refresh --seed

This will use the migrations in `database/migrations` and the data contained within the various seeders at `database/seeds` to both build and populate the database.


### Cron

Regular tasks will be dealt with by lumen, set this cron to run every minute to allow the app to delegate any jobs.

```
* * * * * php /path/to/artisan schedule:run >> /dev/null 2>&1
```

### Docs
<link>
