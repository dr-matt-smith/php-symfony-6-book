# Connect to and create our MySQL database

We need to set things up so our PHP web application can communicate with MySQL and also setup our example database. 

Do the following:

1. Check the credentials in the `.env` file match your setup. If you are using the default MySQL server settings, the only thing you may need to change are the password for the `root` user:



3. Start up MySQL Workbench (`root` password was `Pass$$` on college computers last time I checked - you should know what ever root password is on your own computer - and I do recommend you HAVE a password for the MySQL root user ...)

    - you may need to 'Clear the Vault' before being able to run an instance of MySQL ...


That's it - PHP should now be able to communicate with MySQL - let's find out ...

Tell Symfony to create its database:

```bash
    $ symfony console doctrine:database:create

    Created database 'crud01' for connection named default
```

(or you can use the 2-letter abbreviated version: `symfony console do:da:cr`)

If you see the `created database` message then things are going well.

## The 2019 PHP-MySQL issue

NOTE:

- Due to a change in MySQL during 2019 we may need to run a special command in MySQL to allow PHP programs to communicate with MySQL:

- in an SQL window in MySQL workbench execute the following command

    ```sql
      alter user 'root'@'localhost' identified with mysql_native_password by 'Pass$$';
    ```

this should solve the problem (althogh it may have been fixed in a more recent version of MySQL/PHP ...)

## Ensure the `/migrations` folder is empty
If there is a folder `/migrations` DELETE its contents (since we have a new database, we don't want any old migrations to mess it up)

- but do not delete the directory itself, otherwise it will through an error when trying to setup the database

## Migrating code Entities to DB tables

Now do the following:

1. Create a new migration:

    `symfony console make:migration`

   - (or you can use the 2-letter abbreviated version: `symfony console ma:mi`)

   - if you are curious, you are creating SQL to create DB tables for the classes in `/src/Entity` in this step..., and you can see this created SQL code in the PHP classes created in the `/migrations` folder ...
   

2. Run the migration:

    `symfony console doctrine:migrations:migrate`

   - (or you can use the 2-letter abbreviated version: `symfony console do:mi:mi`)
        
        - say `y` when asked

## Loading initial data into the Database

Now do the following:

4. Load test data (fixtures)

    `symfony console doctrine:fixtures:load`

   - (or you can use the 2-letter abbreviated version: `symfony console do:fi:lo`)

        - say `y` when asked

   - if you are curious, you are running the classes in `/src/DataFixtures` in this step...
    
5. Now in MySQL workbench let's see what we've created, execute SQL:

    ```sql
       use crud01;
   
       select * from user;
   ```
    
See Figure \ref{db_users} shows a screenshot of our database contents in the MySQL Workbench DB client.

![User details in the database. \label{db_users}](./03_figures/appendices/crud26_workbench_crud01.png){ width=100% }

## SOLVING COMMON PROBLEMS: Error(s) when executing MIGRATIONS (table already exists etc.)
Each Migration is the incremental bit of SQL that needs to be executed to update the MySQL database structure to match our PHP Entity Classes.

Sometimes things will get out of synch - so that when we try to execute a migration with: doctrine:migrations:migrate, we get some errors about tables/properties already existing

The quickest and easiest way to get past this problem is to start again with a BRAND NEW EMPTY database, and NO MIGRATIONS - do this with the following 3 steps:

1. Change the database name in file `.env`

    - personally I just add 1 to the number of this database, e.g. change `crud01` to `crud02` and so on

2. Delete all historic migrations, just delete ALL files inside folder  `/migrations`

   - NOTE: Do **not** delete the folder itself, otherwise you'll get errors when you try to create new migrations ...

3. Run your steps to create new db / create SQL for migrations / run SQL for migrations / load any fixture data:

    - create a new database (using the credentials in `.env`):

        ```bash
        symfony console doctrine:database:create
        ```
    
    - write the SQL we need to create database to match the classes in `/src/Entity`:

        ```bash
        symfony console make:migration
        ```
    
    - execute the SQL to create / alter the tables in the database:

        ```bash
        symfony console doctrine:migrations:migrate
        ```

    - load any startup data defined in our **fixtures** classes:    

        ```bash
        symfony console doctrine:fixtures:load
        ```

Or using the 2-letter abbreviated steps:

```bash
symfony console do:da:cr
symfony console ma:mi
symfony console do:mi:mi
symfony console do:fi:lo
```
