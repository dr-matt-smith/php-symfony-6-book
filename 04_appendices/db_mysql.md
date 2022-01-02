

# Setting up for MySQL Database \label{appendix_db_mysql}

## Declaring the parameters for the database (`parameters.yml`)

Usually the project-specific settings are declared in this file:

```
    /app/config/parameters.yml
```

These parameters are referred to in the more generic `/app/config/config.yml` - which for MySQL projects we don't need to touch.


The simplest way to connect your Symfony application to a MySQL database is by setting the following variables in `parameters.yml` (located in (`/app/config/`):

```
    # This file is auto-generated during the composer install
    parameters:
        database_host: 127.0.0.1
        database_port: null
        database_name: symfony_book
        database_user: root
        database_password: null
```

Note, you can learn move about `parameters.yml` and `config.yml` in Appendix \ref{appendix_parameters}.

You can replace `127.0.0.1` with `localhost` if you wish. If your code cannot connect to the database check the 'port' that your MySQL server is running at (usually 3306 but may be different, for example my Mac MAMP server uses 8889 for MySQL for some reason). So my parameters look like this:

```
    parameters:
        database_host:     127.0.0.1
        database_port:     8889
        database_name:     symfony_book
        database_user:     symfony
        database_password: pass

```

We can now use the Symfony CLI to **generate** the new database for us. You've guessed it, we type:

```bash
    $ php bin/console doctrine:database:create
```

You should now see a new database in your DB manager. Figure \ref{new_db} shows our new `symfony_book` database created for us.

![CLI created database in PHPMyAdmin. \label{new_db}](./03_figures/database/1_new_db.png)

**NOTE** Ensure your database server is running before trying the above, or you'll get an error like this:

```
    [PDOException] SQLSTATE[HY000] [2002] Connection refused
```

now we have a database it's time to start creating tables and populating it with records ...
