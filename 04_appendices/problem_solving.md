

# Solving problems with Symfony \label{appendix_problem_solving}


## No home page loading

Ensure web server is running (either from console, or a webserver application with web root of the project's `/public` directory).

If Symfony thinks you are in **production** (live public website) then when an error occurs it will throw a 500 server error (which a real production site would catch and display some nicely sanitised message for website visitors).

Since we are in **development** we want to see the **details** of any errors. We set the environment in the `.env` file. For development mode you should see the following in this file:

```bash
    APP_ENV=dev
```

In development you should then get a much more detailed description of the error (including the class / line / template causing the problem etc.).

Also, if you know where your server error logs are stored, you can see the errors written to the log file. Symfony lots are usually create in `/var/log`.

## "Route not Found" error after adding new controller method

If you have issues of Symfony not finding a new route you've added via a controller annotation comment, try the following.

It's a good idea to **CLEAR THE CACHE** when addeding/changing routes, otherwise Symfony may not recognised the new or changed routes ... Either manually delete the `/var/cache` directory, or run the `cache:clear` console command:

```bash
    $ php bin/console cache:clear

    // Clearing the cache for the dev environment with debug true
    [OK] Cache for the "dev" environment (debug=true) was successfully cleared.
```

Symfony caches (stores) routing data and also rendered pages from Twig, to speed up response time. But if you have changed controllers and routes, sometimes you have to manually delete the cache to ensure all new routes are checked against new requests.

## Issues with timezone

Try adding the following construction to `/app/AppKernel.php` to solve timeszone problems:

```php
    public function __construct($environment, $debug)
    {
        date_default_timezone_set( 'Europe/Dublin' );
        parent::__construct($environment, $debug);
    }
```

## Issues with Symfony 3 and PHPUnit.phar

Symfon 3.2 has issues with PHPUnit (it's PHPUnit's fault!). You can solve the problem with the Symphony PHPUnit `bridge` - which you install via Composer:

```bash
    composer require --dev symfony/phpunit-bridge
```

You then execute your PHPUnit test with the `simple-phpunit` command in `/vendor/bin` as follows:

```bash
    ./vendor/bin/simple-phpunit
```

Source:

- [Symfony Blog December 2016](http://symfony.com/blog/how-to-solve-phpunit-issues-in-symfony-3-2-applications)

## PHPUnit installed via Composer

To install PHPUnit with Composer run the following Composer update CLI command:

```bash
    composer require --dev phpunit/phpunit ^6.1
```

To run tests in directory `/tests` exectute the following CLI command:

```bash
    ./vendor/bin/phpunit tests
```

Source:

- [Stack overflow](https://stackoverflow.com/questions/13764309/how-to-use-phpunit-installed-from-composer)

As always you can add a shortcut script to your `composer.json` file to save typing, e.g.:

```json
    "scripts": {
        "run":"php bin/console server:run",
        "test":"./vendor/bin/phpunit tests",

        ...
    }
```

