
# Symfony 4 - Quickstart CRUD generation\label{appendix_crud_quickstart}

## What we'll create

This quickstart is a set of steps to create a database-drive website from scratch, with web forms for CRUD (Create-Read-Update-Delete). You aren't expected to understand all this if you're new to Symfony, but hopefully you'll see how powerful using a framework can be, and see how object-orientede programming and databases tie together with website front ends to make creating powerful applications straightforward, when you have a clear idea of the features and user-interaction you wish your application to support.

Note: Much of what we are doing is documented in the **Doctrine** pages of the Symfony website, if you wanted to read more around this topic:

    - (https://symfony.com/doc/current/doctrine.html)[https://symfony.com/doc/current/doctrine.html]

## Software tools

You will need the following working on your computer:

1. the Symfony command line tool
1. The SQLite PHP database extension

TUDublin computers should already have the Symfony command line setup and working (we'll test it in the next step).

This worksheet will show you how to enable SQLite on a University computer (if it's not already setup).

## Open a CLI terminal window

Many professional software tools run from the command line. A CLI is a Command Line Interface, in other words a text-based terminal window. On a Windows computer startup a `cmd` window. On a Mac run the `Terminal` application.

One aspect of working with a CLI terminal is that we need to `cd` (change directory) to the location on the computer's hard disk where we want to work. On University computers it's often easier to work on the `Desktop`, and later you can backup your project folder to a network drive, USB, or cloud disk like OneDrive.

### Windows `cd` to Desktop in command window

Do the following to get to the `Desktop` folder at the command line in Windows:

1. start `cmd` terminal window

1. ensure you are on the `C:` drive, if not, change to the `C:` drive by entering `C:` and pressing `<RETURN`>

    - you should now see `C:>` as your command line prompt
    
1. change into the `Desktop` folder for your logged-on user, by typing the following

    ```bash
        C:> cd \Users\B11223344\Desktop
    ```
    
    - of course, you need to replay `B11223344` with your own student number
    
1. you should now be at your user's `Desktop` in the command line - so when you create a project folder you should see it on your desktop

### Mac OSX `cd` to Desktop in Terminal window

Do the following to get to the `Desktop` folder at the command line in Windows:

1. start `Terminal` application

1. change into the `Desktop` folder for your logged-on user, by typing the following

    ```bash
        matt$ cd ~/Desktop
    ```

1. you should now be at your user's `Desktop` in the command line - so when you create a project folder you should see it on your desktop



## Check computer has requirements to run Symfony

At the CLI terminal command line type:

```bash
    symfony check:requirements
```

You should see something like the following appear - the importart bit is the message saying `Your system is ready to run Symfony projects`:

```bash
    C:\Desktop> symfony check:requirements
    
    Symfony Requirements Checker
    ~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    > PHP is using the following php.ini file:
    /usr/local/php5/lib/php.ini
    
    > Checking Symfony requirements:
    ......................W.........
    [OK] Your system is ready to run Symfony projects  <<<<<<< yay -Symfony is supported!!
    
    Optional recommendations to improve your setup
    ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
     * a PHP accelerator should be installed
       > Install and/or enable a PHP accelerator (highly recommended).
```

Don't worry about any optional recommendations.

## Enabling the SQLite PHP database extension (Windows)

If Symfony is working, you have PHP setup on your computer. However, the SQLite database extension may not be setup.

You can either work ahead, hoping it is setup, and fix it if you hit a problem when trying to create a database. Or you can check, and fix it now.

PHP extensions are already installed with PHP, but may not be activated. All we have to do is ensure there is no semi-colon character `;` at the beginning of the line `extension=php_pdo_sqlite.dll'. 

Do the following:

1. Use Notepad++ to open file `C:\php\php.ini`

1. Search (`<CTRL>-F`) for `pdo_sqlite`

1. If the line reads `extension=php_pdo_sqlite.dll' remove the first character and save the changed file.

1. That's it - you have now enabled SQLite for PHP applications.

If you want to enable the SQLite database as well, then do the same for the line saying `extension=php_pdo_sqlite.dll'.

## Create new project named "student1"

At the CLI terminal let's tell the Symfony command line tool to create a new, website application skeleton for us, in a new folder to be nbamed `student1` (you can choose a different project-folder name if you wish):

1. At the terminal type the following:

    ```bash
       symfony new --full student1
    ```   

2. You should see the following as Symfony downloads a skeleton project - it might take a few minutes depending on the speed of your computer, internet connection, and whether it's the first time you've done this (later times may use 'cached' remembnerd copies of some files, and so be quicker!):
    
    ```bash
       C:\Users\B11223344\Desktop> symfony new --full student1
    
       * Creating a new Symfony project with Composer
         (running omposer create-project symfony/website-skeleton student1)
       
       * Setting up the project under Git version control
         (running git init student1)
                                                                                                                               
        [OK] Your project is now ready in C:\Users\B11223344\Desktop\student1 
    ```

## Open project folder `student1` in your IDE editor

At this point we should now open our new project folder in an IDE editor like PHPStorm. Since we are going to want to examine and edit the project code.

Do the following:

1. Start your IDE editor

1. Use your editor to open your Desktop project folder `student1`

1. Either open a terminal window in your IDE, or keep your terminal application window open, and `cd` into the `student1` folder



Figure \ref{new_project} shows a screenshot of PHPStorm and our new skeleton project.

![PHPStorm IDE new project. \label{new_project}](./03_figures/appendices/crud01_new_project.png){ width=60% }

## Change project to work with a simple SQLite database

Symfony projects provide default settings to work with MySQL databases. But it's quicker to start working with an SQLite database file.

We need to add 1 line to the special `.env` environment settings file. In your IDE open file `.env` and fine this section about the **Doctrine** database settings (around line 20 in the settings file): 

```bash
    ###> doctrine/doctrine-bundle ###
    # Format described at https://www.doctrine-project.org/projects/doctrine-dbal/en/latest/reference/configuration.html#connecting-using-a-url
    # For an SQLite database, use: "sqlite:///%kernel.project_dir%/var/data.db"
    # Configure your db driver and server_version in config/packages/doctrine.yaml
    DATABASE_URL=mysql://db_user:db_password@127.0.0.1:3306/db_name
    ###< doctrine/doctrine-bundle ###
```

We need to add an extra line (to override the default `DATABASE_URL` variable):

```bash
    DATABASE_URL=sqlite:///%kernel.project_dir%/var/data.db
```

Add this extra line so this section now looks as follows:

```bash
    ###> doctrine/doctrine-bundle ###
    # Format described at https://www.doctrine-project.org/projects/doctrine-dbal/en/latest/reference/configuration.html#connecting-using-a-url
    # For an SQLite database, use: "sqlite:///%kernel.project_dir%/var/data.db"
    # Configure your db driver and server_version in config/packages/doctrine.yaml
    DATABASE_URL=mysql://db_user:db_password@127.0.0.1:3306/db_name
    ###< doctrine/doctrine-bundle ###
    DATABASE_URL=sqlite:///%kernel.project_dir%/var/data.db
```

## Create the database

We can now use the command line tool to create the database for the project, using the command `php bin/console doctrine:database:create`:

```bash
    > php bin/console doctrine:database:create
    Created database /Users/matt/Desktop/student1/var/data.db for connection named default
```

We have now created an SQLite database file `var/data.db`. See Figure \ref{sqlite} shows a screenshot of the file in our editor file list.      

![SQLite database file in project files. \label{sqlite}](./03_figures/appendices/crud03_sqlite.png){ width=50% }
        
## Create Student class

Let's create a Student class and generate automatic CRUD web pages. Do the following:

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

We can also use the 'make' command line tool to look at our classes and create the SQL to update our database create/update tables for storing the object data in tables and rows.

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

Now let's tell Symfony to connect to the database and execute the migration SQL - to actually create the new `Student` table.

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

That's it - we have now created a table in out databasde to match our PHP entity class.

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

## Run the Symfony web sever

Let's run the web server on our machine (`localhost:8000`) by entering terminal command `symfony serve --no-tls`.

Note, you might get some warnings/info messages about version of PHP etc. - just ignore them!

```bash
    > symfony serve --no-tls
    Sep 27 07:22:19 |DEBUG| PHP    Using PHP version 7.3.8 (from default version in $PATH) 
    Sep 27 07:22:19 |INFO | PHP    listening path="/usr/local/php5-7.3.8-20190811-205217/sbin/php-fpm" php="7.3.8" port=52271
    Sep 27 07:22:19 |DEBUG| PHP    started 
    Sep 27 07:22:19 |INFO | PHP    'user' directive is ignored when FPM is not running as root 
    Sep 27 07:22:19 |INFO | PHP    'group' directive is ignored when FPM is not running as root 
    Sep 27 07:22:19 |INFO | PHP    fpm is running, pid 98665 
    Sep 27 07:22:19 |INFO | PHP    ready to handle connections 
                                                                                                                            
     [OK] Web server listening on http://127.0.0.1:8000 (PHP FPM 7.3.8)                                                     
```

## Visit the home page `localhost:8000`

Open a web browser and visit our website home page at `http://localhost:8000`.

Since we didn't create a home page, we'll see a default Symfony home page. See Figure \ref{homepage} shows a screenshot of PHPStorm and our new class PHP code.

![Default Symfony home page. \label{homepage}](./03_figures/appendices/crud04_homepage.png){ width=75% }

## Visit our generated `Student` crud pages at `/student`

Let's visit our generated CRUD pages, these can be found by adding `/student` at the end of the URL. See Figure \ref{student1} shows a screenshot of PHPStorm and our new class PHP code.

![Student CRUD list page. \label{student1}](./03_figures/appendices/crud05_student.png){ width=75% }

Click `Create new` and add a student. Then try clicking edit, and change some values or delete it and create it again.

You should find you have a fully working web-based CRUD interface to your database.

See Figure \ref{student_list} shows a screenshot of several students having been created (and yes, my grandmother did live to 96!).

![Several students created with web CRUD. \label{student_list}](./03_figures/appendices/crud06_studentList.png){ width=75% }



## Databases are **persistent**

Kill the Symfony web server at the command line by pressing `<CTRL>-C`. Then quit the PHPStorm IDE application.

You could also go have a cup of coffee, or perhaps shut down and restart your computer.

Then restart the PHPStorm editor, and restart the web server with `symfony serve --no-tls`.

Now open a web browser to URL `http://localhost:8000/student` and you should see that the students you created in your database are still there.

Any changes we make are remembered (persisted) as part of our `var/data.db` database file.

## How to make it look nice with Bootstrap CSS

Let's add the Bootsrap CSS to our project - so it looks more professional.



Learn more about the Bootstrap 4 theme on the Symfony documentation pages:

- [https://symfony.com/doc/current/form/bootstrap4.html](https://symfony.com/doc/current/form/bootstrap4.html)

Do the following steps...

### Configure Twig to use the Bootstrap theme

Well Symfony to generate forms using the Bootstrap theme by adding:
 
```twig
    form_themes: ['bootstrap_4_layout.html.twig']
``` 

to `/config/packages/twig.yml` file. So this file should now look as follows;

```yaml
    twig:
        paths: ['%kernel.project_dir%/templates']
        debug: '%kernel.debug%'
        strict_variables: '%kernel.debug%'
        form_themes: ['bootstrap_4_layout.html.twig']
```

### Add the Bootstrap CSS import into our base Twig template

The Bootstrap QuickStart tells us to copy the CSS `<link>` tag from here:

- [https://getbootstrap.com/docs/4.1/getting-started/introduction/#css](https://getbootstrap.com/docs/4.1/getting-started/introduction/#css)

into the CSS part of our `/templates/base.html.twig` Twig template. Add this `<link>` tag just before the `stylesheets` block:

```twig
    <!DOCTYPE html> <html>
    <head>
        <meta charset="UTF-8" />
        <title>MGW - {% block pageTitle %}{% endblock %}</title>
        
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
        {% block stylesheets %}{% endblock %}
        
    </head>
   ...

```


### Add the Bootstrap JavaScript import into our base Twig template.

The Bootstrap QuickStart tells us to copy the JS `<script>` tags from here:

- [https://getbootstrap.com/docs/4.1/getting-started/introduction/#js](https://getbootstrap.com/docs/4.1/getting-started/introduction/#js)

into the last part of the `<body>` element in `templates/base.html.twig` Twig template. Add these `<script>` tags just after the `javascripts` block:

```twig
    ...
    
    <body>
        {% block body %}{% endblock %}
    
        {% block javascripts %}{% endblock %}
    
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
    
    </body>
</html>

```

### Adding elements for navigation and page content

Let's ensure main `body` content of every page is inside a Bootstrap element. We need to wrap a Bootstrap container and row divs around the `body` Twig block:

In file `templates/base.html.twig` replace line:

```twig
    {% block body %}{% endblock %}
```

with the following (i.e. the same linem, but with Boostrap `div`s wrapped around it):

```twig

    <div class="container">
        <div class="row">
            <div class="col-sm-12">

            {% block body %}{% endblock %}

            </div>
        </div>
    </div>
```

### Full listing of updated `templates/base.html.twig`

Your websites **basic template** file (`templates/base.html.twig`) should now look like this:

```twig
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>{% block title %}Welcome!{% endblock %}</title>
        {% block stylesheets %}{% endblock %}
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    </head>
    <body>

        <div class="container">
            <div class="row">
                <div class="col-sm-12">

                    {% block body %}{% endblock %}

                </div>
            </div>
        </div>
        {% block javascripts %}{% endblock %}

        <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
    </body>
</html>
```

### Remove CRUD and regenerate with Bootstrap

We now need to delete our generated CRUD, and re-genereate it. The generation logic will see the Bootrap theme, and style our web forms to look nice with appropriate Bootstap CSS.

1. Delete the old CRUD files:

    - Delete the `student` folder in `templates`

    - Delete the `StudentController.php` class file in `src/Controller`

    - Delete the `StudentType.php` class file in `src/Form`

2. Now re-generate the crud

    - run terminal command `php bin/console make:crud Student`
    
Run the web server again, visit `/student` and voila! we have Bootstrap themed web-forms!

See Figure \ref{bootstrap} shows a screenshot of several students having been created (and yes, my grandmother did live to 96!).

![CRUD with Bootstrap theme. \label{bootstrap}](./03_figures/appendices/crud07_bootstrap.png){ width=75% }


