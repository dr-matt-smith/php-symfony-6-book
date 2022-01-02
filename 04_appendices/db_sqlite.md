

# Setting up for SQLIte Database \label{appendix_db_sqlite}

## NOTE regarding FIXTURES

If you are using the Doctrine Fixtures Bundle, install that first **before** changing paramaters and config for SQLite. The fixtures bundle assumes MySQL, and will overwrite some of the parameters during installation.

If that does happen, you'll just have to repeat the steps in this Appdendix to set things back to SQLite after fixtures installation.

## SQLite suitable for most small-medium websites

For small/medium projects, and learning frameworks like Symfony, it's often simplest to just use a file-based SQLite database.

Learn more about SQLite at the project's website, and their discussion of when SQLite is a good choices, and when a networked DBMS like MySQL is more appropriate:

- [SQLite website](https://www.sqlite.org/)

- [Appropriate Uses For SQLite](http://www.sqlite.org/whentouse.html)

## Create directory where SQLite database will be stored

Setting one up with Symfony is **very** easy. These steps assume you are gong to use an SQLite database file named `data.sqlite` located in directory `/var/data`.

Our first step to configuring a Symfony project to work with SQLite is to ensure the directory exists where the SQLIte file is to be created. The usual location for Symfony projects is `/var/data`. So create directory `data` in `/var` if it doesn't already exist in your project.

## Declaring the parameters for the database (`parameters.yml`)

In `/app/parameters.yml` replace the default `database_host/name/user/password` parameters with a single parameter `database_path` as follows:

    ```yaml
        parameters:
            database_path: ../var/data/data.sqlite
            mailer_transport: smtp
            mailer_host: 127.0.0.1
            etc.
    ```

## Setting project configuraetion to work with the SQLite database driver and path (`/app/config/config.yml`)

In `/app/config.yml` change the `doctrine` settings **from** these MySQL defaults:

    ```yaml
        # Doctrine Configuration
        doctrine:
            dbal:
                driver:   pdo_mysql
                host:     "%database_host%"
                port:     "%database_port%"
                dbname:   "%database_name%"
                user:     "%database_user%"
                password: "%database_password%"
                charset:  UTF8
    ```

**to** these SQLite settings:

    ```yaml
        # Doctrine Configuration
        doctrine:
            dbal:
                driver:   pdo_sqlite
                path:     "%kernel.root_dir%/%database_path%"
    ```



That's it! You can now tell Symfony to create your database with CLI command:

```bash
    php bin/console doctrine:database:create
```

You'll now have an SQLite database file at `/var/data/data.sqlite`. You can even use the PHPStorm to open and read the DB for you. See Figures \ref{phpstorm_new_db} and \ref{sqlite_open}.

![Open SQLite view in PHPMyAdmin. \label{phpstorm_new_db}](./03_figures/database/8_phpstorm_database_sm.png)

![Viewing `/var/data.sqlite` in PHPStorm. \label{sqlite_open}](./03_figures/database/9_sqlite_in_phpstorm_sm.png)
