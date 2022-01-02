# Create your own CRUD for a Student entity class

## Create Student class

Let's create a Student class and generate automatic CRUD web pages. 

![Class diagram for Student entity.](./03_figures/app_crud/student_ClassDiagram1.png){ width=75% }

Do the following:

1. At the command line type:

    ```bash
        php bin/console make:entity Student
    ```
   
   You should see the following:
   
   ```bash
     > php bin/console make:entity Student
    
     created: src/Entity/Student.php
     created: src/Repository/StudentRepository.php
     
     Entity generated! Now let's add some fields!
     You can always add more fields later manually or by re-running this command.
   ```
   
    - notice that it tells us that is has created 2 new classes `src/Entity/Student.php` and `src/Repository/StudentRepository.php`
        
        - `Student.php` is a simple class with private propetries and getters/setters, with special 'annotation' comments so these objects can map directly to rows in a database table...

1. Now we need to ask this console 'make' tool to add an integer `age` property for us:

    - we need to enter the property name `age`
    
    - we need to specify its data type `integer`
    
    - (to keep things simple) we don't mind if our properies start as null (just press `<RETURN>` for this question)
    
    - you should see the following when answering these questions at the `make` command tool prompt:

    ```bash
         New property name (press <return> to stop adding fields):
         > age
        
         Field type (enter ? to see all types) [string]:
         > integer
        
         Can this field be null in the database (nullable) (yes/no) [no]:
         > 
        
         updated: src/Entity/Student.php
    ```
   
1. Now add a string `name` property:

    - we need to enter the property name `name`
    
    - we need to specify its data type `string` (since default just press `<RETURN>`)
    
    - accept default string length of 255 (since default just press `<RETURN>`)
    
    - can be nullable  (since default just press `<RETURN>`)
    
    - you should see the following when answering these questions at the `make` command tool prompt:

    ```bash
         Add another property? Enter the property name (or press <return> to stop adding fields):
         > name
        
         Field type (enter ? to see all types) [string]:
         > 
        
         Field length [255]:
         > 
        
         Can this field be null in the database (nullable) (yes/no) [no]:
         > 
        
         updated: src/Entity/Student.php
    ```
  
 1. That's all our fields created, so just press `<RETURN>` to complete creation of our entity:
 
     ```bash
            Add another property? Enter the property name (or press <return> to stop adding fields):
            > 
                      
             Success! 
                      
            Next: When you're ready, create a migration with make:migration
       ```

## Take a look at the created entity class

Take a look at what's been created for us: `src/Entity/Student.php`. If you ignore the comments, mostly this is a class 


See Figure \ref{student} shows a screenshot of PHPStorm and our new class PHP code.

![New Student.php entity. \label{student}](./03_figures/appendices/crud02_studentEntity.png){ width=90% }

## Make 'migration' SQL to create database to correspond to our entity class

We can also use the 'make' command line tool to look at our classes and create the SQL commands we need to update our database create/update tables for storing the object data in tables and rows.

Enter the following at the command line `php bin/console make:migration`:

```bash
    > php bin/console make:migration
               
      Success! 
    
     Next: Review the new migration "src/Migrations/Version20190927055812.php"
     Then: Run the migration with php bin/console doctrine:migrations:migrate
     See https://symfony.com/doc/current/bundles/DoctrineMigrationsBundle/index.html
```

If you look inside the newly created file you'll see a line like this showing the SQL generated to create a database table to match our `Student.php` class:

```php
    $this->addSql(
        'CREATE TABLE student (
            id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, 
            age INTEGER NOT NULL, 
            name VARCHAR(255) NOT NULL
        )'
    );
```

## Execute our 'migration' SQL to create/update database

Now let's tell Symfony to connect to the database and execute the migration SQL - to actually **create** the new `Student` table in the database.

We need to enter the terminal command `php bin/console doctrine:migrations:migrate`:

```bash
    > php bin/console doctrine:migrations:migrate
                                                                  
        Application Migrations                    
                                                                  
    WARNING! You are about to execute a database migration that could result in schema changes and data loss. 
    Are you sure you wish to continue? (y/n)
```

At this point we must enter `y` to go ahead - saying we are happy for our database structure to be chagned by exectuing our migration SQL:

```bash
    WARNING! You are about to execute a database migration that could result in schema changes and data loss. 
    Are you sure you wish to continue? (y/n)y

    Migrating up to 20190927055812 from 0
    
      ++ migrating 20190927055812
         -> CREATE TABLE student (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, 
        age INTEGER NOT NULL, name VARCHAR(255) NOT NULL)
    
      ++ migrated (took 64.4ms, used 18M memory)
      ------------------------
      ++ finished in 70.8ms
      ++ used 18M memory
      ++ 1 migrations executed
      ++ 1 sql queries
```

That's it - we have now created a table in out database to match our PHP entity class.

## Generate the **CRUD** web form for class `Student`

Let's generate some HTML and PHP code for a web form to list and create-read-update-delete data from our database.

We need to execute this command to create that code `php bin/console make:crud Student`:

```bash
    > php bin/console make:crud Student
    
     created: src/Controller/StudentController.php
     created: src/Form/StudentType.php
     created: templates/student/_delete_form.html.twig
     created: templates/student/_form.html.twig
     created: templates/student/edit.html.twig
     created: templates/student/index.html.twig
     created: templates/student/new.html.twig
     created: templates/student/show.html.twig
               
      Success! 

     Next: Check your new CRUD by going to /student/
```


## Visit our generated `Student` crud pages at `/student`

Let's visit our generated CRUD pages, these can be found by adding `/student` at the end of the URL. 

Click `Create new` and add a student. Then try clicking edit, and change some values or delete it and create it again.

You should find you have a fully working web-based CRUD interface to your database.

See Figure \ref{student_list} shows a screenshot of several students having been created (and yes, one of my grandmothers did live to 96!).

![Screenshot of nice lookling Bootstrap style admin CRUD pages. \label{student_list}](./03_figures/app_crud/crud07_bootstrap.png){ width=75% }



## Databases are **persistent**

Kill the Symfony web server at the command line by pressing `<CTRL>-C`. Then quit the PHPStorm IDE application.

You could also go have a cup of coffee, or perhaps shut down and restart your computer.

Then restart the PHPStorm editor, and restart the web server with:

```bash
    symfony serve
```

Now open a web browser to URL `http://localhost:8000/student` and you should see that the students you created in your database are still there.

Any changes we make are remembered (persisted) as part of our database.
