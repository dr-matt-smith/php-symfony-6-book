# Connect to and create our MySQL database

We need to set things up so our PHP web applicaton can communicate with MySQL and also setup our example database. Do the following:

1. Start up MySQL Workbench (`root` password is `Pass$$` on college computers ...)

    - you may need to 'Clear the Vault' before being able to run an instance of MySQL ...

1. Due to a change in MySQL during 2019 we need to run a special command in MySQL to allow PHP programs to communicate with MySQL:

    - in an SQL window in MySQL workbench execute the following command
    
        ```sql
          alter user 'root'@'localhost' identified with mysql_native_password by 'Pass$$';
        ```
      
That's it - PHP should now be able to communicate with MySQL - let's find out ...

Tell Symfony to create its database:

```bash
    $ php bin/console doctrine:database:create

    Created database 'crud01' for connection named default
```

If you see the `created database` message then things are going well.

Now do the following:

1. If there is a folder `src/Migrations` DELETE it (since we have a new database, we don't want any old migrations to mess it up)

1. Create a new migration:

    `php bin/console make:migration`

1. Run the migration:

    `php bin/console doctrine:migrations:migrate`

        - say `y` when asked
    
1. Load test data (fixtures)

    `php bin/console doctrine:fixtures:load`

        - say `y` when asked
    
1. Now in MySQL workbench let's see what we've created, executre SQL:

    ```sql
       use crud01;
   
       select * from user;
   ```
    
See Figure \ref{db_users} shows a screenshot of our database contents in the MySQL Workbench DB client.

![User details in the database. \label{db_users}](./03_figures/appendices/crud26_workbench_crud01.png){ width=100% }

## SOLING COMMON PROBLEMS: Error(s) when executing MIGRATIONS (table already exists etc.)
Each Migration is the incremental bit of SQL that needs to be executed to update the MySQL database structure to match our PHP Entity Classe.

Sometimes things will get out of synch - so that when we try to execute a migration with: doctrine:migrations:migrate, we get some errors about tables/properties already existing

The quickest and easiest way to get past this problem is to start again with a BRAND NEW EMPTY database, and NO MIGRATIONS - do this with the following 3 steps:

1. Change the database name in file `.env`

    - personally I just add 1 to the number of this database, e.g. change `crud01` to `crud02` and so on

1. Delete all historic migrations, just delete the whole folder  `/src/Migrations`

1. Run your steps to create new db / create SQL for migrations / run SQL for migrations / load any fixture data:

    - create a new database (using the credentials in `.env`):

        ```bash
        php bin\console doctrine:database:create
        ```
    
    - write the SQL we need to create database to match the classes in `/src/Entity`:

        ```bash
        php bin\console make:migration
        ```
    
    - execute the SQL to create / alter the tables in the database:

        ```bash
        php bin\console doctrine:migrations:migrate
        ```

    - load any startup data defined in our **fixtures** classes:    

        ```bash
        php bin\console doctrine:fixtures:load
        ```

