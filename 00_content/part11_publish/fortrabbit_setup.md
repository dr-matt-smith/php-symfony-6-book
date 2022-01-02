
# Setting up project ready for Fortrabbit


## Updating the names of our MySQL variables to match Fortrabbit ones

By simply changing the names (identifiers) of the variables in our `.env` file, it means our application will work locally with these settings and also work with no further changes when published to Fortrabbit. 


Choose a **NEW** database name, e.g. I've chosen `week10demo` here. Now edit your project's `.env` file to now use variables `MYSQL_USER`, `MYSQL_PASSWORD`, `MYSQL_HOST` and `MYSQL_DATABASE` as follows, with **your** local root password and new database name:

```dotenv
    MYSQL_USER=root
    MYSQL_PASSWORD=passpass
    MYSQL_HOST=127.0.0.1:3306
    MYSQL_DATABASE=week10demo
    DATABASE_URL=mysql://${MYSQL_USER}:${MYSQL_PASSWORD}@${MYSQL_HOST}/${MYSQL_DATABASE}
```

See Figure \ref{mysqlvariables} to see these Fortrabbit MySQL variables for Symfony project.

![The Fortrabbit MySQL environment variables.\label{mysqlvariables}](./03_figures/part11/7_mysql_variables.png)


## Create new DB locally, and make fresh migrations 

Since both locally, and remotely we'll have a new DB, do the following to keep things in step:

1. Delete the `/src/Migrations` folder

1. Create the new local database schema with `php bin/console doctrine:database:create`

1. Create a migration for this new database with `php bin/console make:migration`

We now have a new clean migration ready to use with our remote database.

## Creating the `/public/.htaccess` Apache server routing file

This is a solved problem, since there is a Symfony community Composer Flex "recipe" to copy into our project the file we need.

Type the following a the command line:

```bash
    composer require symfony/apache-pack
```

You'll be asked to say "yes" since this is a community contribution and not officially part of the Symfony project:

```bash
    $ composer require symfony/apache-pack

    Using version ^1.0 for symfony/apache-pack
    ./composer.json has been updated
    Loading composer repositories with package information
    Updating dependencies (including require-dev)
    Restricting packages listed in "symfony/symfony" to "5.0.*"
    Package operations: 1 install, 0 updates, 0 removals
      - Installing symfony/apache-pack (v1.0.1): Loading from cache
    Writing lock file
    Generating autoload files
    ocramius/package-versions: Generating version class...
    ocramius/package-versions: ...done generating version class
    Symfony operations: 1 recipe (b11f4293313a650b4596551c0c2bb403)
      -  WARNING  symfony/apache-pack (>=1.0): From github.com/symfony/recipes-contrib:master
        The recipe for this package comes from the "contrib" repository, which is open to community contributions.
        Review the recipe at https://github.com/symfony/recipes-contrib/tree/master/symfony/apache-pack/1.0
    
        Do you want to execute this recipe?
        [y] Yes
        [n] No
        [a] Yes for all packages, only for the current installation session
        [p] Yes permanently, never ask again for this project
        (defaults to n): y
```

Say "y" here!

```bash
      - Configuring symfony/apache-pack (>=1.0): From github.com/symfony/recipes-contrib:master
    Executing script cache:clear [OK]
    Executing script assets:install public [OK]
    
    Some files may have been created or updated to configure your new packages.
    Please review, edit and commit them: these files are yours.
```

You should then see a new file `.htaccess` in the `/public` folder. See Figure \ref{htaccess}.

![Screenshot of recipe-creatred `/public/.htaccess` file.\label{htaccess}](./03_figures/part11/11_htaccess.png)


That's it - we've now prepared our local Symfony project for publishing at Fortrabbit!
