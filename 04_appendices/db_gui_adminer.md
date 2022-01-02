

# Setting up Adminer for DB GUI interaction \label{appendix_adminer}

## Adminer - small and simple DB GUI

Adminer is a lightweight PHP, web-based GUI for DB interaction. It supports both MySQL and SQLite.

Figure \ref{fxtures2_in_db} shows  **Adminer** listing 2 user records created from Symofny Doctrine fixtures.

![Using CLI to load database fixtures. \label{fxtures2_in_db}](./03_figures/database/11_data_in_db_sm.png)

## Getting Adminer

Download Adminer from the project website. I recommedn you get the English only version - it's smaller...

- [Adminer.org website](https://www.adminer.org/)

## Setting up

Extract the file to a suitable location. For example you could create an '/amdminer' directory in your current project:

```
    .../project/adminer/adminer.php
```

To keep things simple, and also to remove the login requiremnt for SQLIte access, create file `index.php` in your Adminer directory, containing the following:

```php
    <?php
    // index.php

    function adminer_object()
    {
        class AdminerSoftware extends Adminer
        {
            function login($login, $password) {
                return true;
            }
        }
        return new AdminerSoftware;
    }

    include __DIR__ . "/adminer.php";
```

## Running Adminer

Since we have an `index.php` page, we just need to run a web server pointing its root to our Adminer directory. Perhaps the simplest way to do this is with the built-in PHP server, e.g.:

```
    php -S localhost:3306 -t ./adminer
```

To save typing, you could add a script alias to  your `composer.json` file:

```json
    "adminer":"php -S localhost:3306 -t adminer",
```

When run, choose the appropriate DBMS from the dropdown menu (e.g. **SQLite 3**), and enter the required credentials. For SQLite all we need to enter is the path to the location of the SQLite database file, e.g.:

```
    /Users/matt/Desktop/kill/symfony/product1/var/data/data.sqlite
```