

# The fully-featured Symfony 4 demo\label{appendix_sf_demo}

## Visit Github repo for full Symfony demo
Visit the project code repository on Github at: [https://github.com/symfony/demo](https://github.com/symfony/demo)

## Git steps for download (clone)
If you have Git setup on your computer (it is on the college computers) then do the following:

- copy the clone URL into the clipboard

- open a CLI (Command Line Interface) window

- navigate (using `cd`) to the location you wish to clone^[For a throw-away exercise like this I just create a directry named `temp` (with `mkdir temp`) and `cd` into that...]

- use `git clone <url>` to make a copy of the project on your computer

```bash
    lab01 $ git clone https://github.com/symfony/demo.git
    Cloning into 'demo'...
    remote: Counting objects: 7165, done.
    remote: Compressing objects: 100% (13/13), done.
    remote: Total 7165 (delta 4), reused 8 (delta 2), pack-reused 7150
    Receiving objects: 100% (7165/7165), 6.79 MiB | 1.41 MiB/s, done.
    Resolving deltas: 100% (4178/4178), done.

    lab01 $
```

## Non-git download
If you don't have Git on your computer, just download and **unzip** the project to your computer (and make a note to get **Git** installed a.s.a.p.!)

## Install dependencies
Install any required 3rd party components by typing `cd`ing into folder `demo` and typing CLI command `composer install`. A **lot** of dependencies will be downloaded and installed!

```bash
    lab01/demo $ composer install
    Loading composer repositories with package information
    Installing dependencies (including require-dev) from lock file
    Package operations: 89 installs, 0 updates, 0 removals
      - Installing ocramius/package-versions (1.2.0): Loading from cache
      - Installing symfony/flex (v1.0.65): Loading from cache
      ...
      - Installing symfony/phpunit-bridge (v4.0.3): Loading from cache
      - Installing symfony/web-profiler-bundle (v4.0.3): Loading from cache
      - Installing symfony/web-server-bundle (v4.0.3): Loading from cache
    Generating autoload files
    ocramius/package-versions:  Generating version class...
    ocramius/package-versions: ...done generating version class

```

## Run the demo
Run the demo with `php bin\console server:run`

(Windows) You may just need to type `bin\console  server:run` since I think there is a `.bat` file in `\bin`:

```bash
    lab01/demo$ php bin/console server:run

     [OK] Server listening on http://127.0.0.1:8000

     // Quit the server with CONTROL-C.

    PHP 7.1.8 Development Server started at Tue Jan 23 08:19:05 2018
    Listening on http://127.0.0.1:8000
    Document root is /Users/matt/Library/Mobile Documents/com~apple~CloudDocs/91_UNITS/UNITS_PHP_4_frmwrks/lab_sheets/web3-lab-sheets-codes/lab01/demo/public
    Press Ctrl-C to quit.
```

## View demo in browser
Open a browswer to `localhost:8000` and play around with the website. Figure \ref{sf_demo} shows a screenshot of the default Symfony page for a new, empty project.

![Default Symfony 4 demo project \label{sf_demo}](./03_figures/app02_sfdemo/sf_demo.png)

## Explore the code in PHPStorm
 Open the code for the project in PHPStorm, and look especially in the `/controllers` and `/templates` directories, to work out what is going on


## Switch demo from SQLite to MySQL

At present the Symfony demo uses the SQLite driver, working with a databse 'file' in `/var/data`.

Let's change this project to work with a MySQL database schema named `demo`.

Do the following:

1. Run MySQL Workbench

2. Change the **URL** for the projects data in `.env` from:

    ```DATABASE_URL=sqlite:///%kernel.project_dir%/var/data/blog.sqlite```

    to

    ```DATABASE_URL="mysql://root:pass@127.0.0.1:3306/demo"```

3. Get the Symfony CLI to create the new database schema, type `php bin/console doctrine:database:create`:

    ```bash
        demo (master) $ php bin/console doctrine:database:create
        Created database `demo` for connection named default

        demo (master) $
    ```

4. Get the Symfony CLI to note any changes that need to happen to the databast to make it match Entites and relationships defined by the project's classes, by typing `php bin/console doctrine:migrations:diff`:

    ```
        demo (master) $ php bin/console doctrine:migrations:diff
        Generated new migration class to "/Users/matt/Library/Mobile Documents/com~apple~CloudDocs/91_UNITS/UNITS_PHP_4_frmwrks/lab_sheets/web3-lab-sheets-codes/lab01/demo/src/Migrations/Version20180127081633.php" from schema differences.

        demo (master) $
    ```

    A migration file has now been created.

5. Run the migration file, by typing `php bin/console doctrine:migrations:migrate` and then typing `y`:

   ```bash
        demo (master) $ php bin/console doctrine:migrations:migrate

        Application Migrations


        WARNING! You are about to execute a database migration that could result in schema changes and data lost. Are you sure you wish to continue? (y/n)y
        Migrating up to 20180127081633 from 0

          ++ migrating 20180127081633

             -> CREATE TABLE symfony_demo_comment (id INT AUTO_INCREMENT NOT NULL, post_id INT NOT NULL, author_id INT NOT NULL, content LONGTEXT NOT NULL, published_at DATETIME NOT NULL, INDEX IDX_53AD8F834B89032C (post_id), INDEX IDX_53AD8F83F675F31B (author_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB
             -> CREATE TABLE symfony_demo_post (id INT AUTO_INCREMENT NOT NULL, author_id INT NOT NULL, title VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, summary VARCHAR(255) NOT NULL, content LONGTEXT NOT NULL, published_at DATETIME NOT NULL, INDEX IDX_58A92E65F675F31B (author_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB
             -> CREATE TABLE symfony_demo_post_tag (post_id INT NOT NULL, tag_id INT NOT NULL, INDEX IDX_6ABC1CC44B89032C (post_id), INDEX IDX_6ABC1CC4BAD26311 (tag_id), PRIMARY KEY(post_id, tag_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB
             -> CREATE TABLE symfony_demo_tag (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_4D5855405E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB
             -> CREATE TABLE symfony_demo_user (id INT AUTO_INCREMENT NOT NULL, full_name VARCHAR(255) NOT NULL, username VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, roles LONGTEXT NOT NULL COMMENT '(DC2Type:json)', UNIQUE INDEX UNIQ_8FB094A1F85E0677 (username), UNIQUE INDEX UNIQ_8FB094A1E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB
             -> ALTER TABLE symfony_demo_comment ADD CONSTRAINT FK_53AD8F834B89032C FOREIGN KEY (post_id) REFERENCES symfony_demo_post (id)
             -> ALTER TABLE symfony_demo_comment ADD CONSTRAINT FK_53AD8F83F675F31B FOREIGN KEY (author_id) REFERENCES symfony_demo_user (id)
             -> ALTER TABLE symfony_demo_post ADD CONSTRAINT FK_58A92E65F675F31B FOREIGN KEY (author_id) REFERENCES symfony_demo_user (id)
             -> ALTER TABLE symfony_demo_post_tag ADD CONSTRAINT FK_6ABC1CC44B89032C FOREIGN KEY (post_id) REFERENCES symfony_demo_post (id) ON DELETE CASCADE
             -> ALTER TABLE symfony_demo_post_tag ADD CONSTRAINT FK_6ABC1CC4BAD26311 FOREIGN KEY (tag_id) REFERENCES symfony_demo_tag (id) ON DELETE CASCADE

          ++ migrated (0.44s)

          ------------------------

          ++ finished in 0.44s
          ++ 1 migrations executed
          ++ 10 sql queries

        demo (master) $
   ```

## Running the tests in the SF4 demo

 The project comes with configuration for `simple-phpuit`. Run this once to download the depedencies:

```bash
    lab01/demo $ vendor/bin/simple-phpunit
       ./composer.json has been updated
       Loading composer repositories with package information
       Updating dependencies
       Package operations: 19 installs, 0 updates, 0 removals
         - Installing sebastian/recursion-context (2.0.0): Loading from cache
         ...
         - Installing symfony/phpunit-bridge (5.7.99): Symlinking from /Users/matt/lab01/demo/vendor/symfony/phpunit-bridge
       Writing lock file
       Generating optimized autoload files

    lab01/demo $

```

## Run the tests

Run the tests, by typing `vendor\bin\simple-phpunit`^[The backlash-forward slash thing is annoying. In a nutshell, for file paths for Windows machines, use backslashes, for everything else use forward slashes. So it's all forward slashes with Linux/Mac machines :-)]

```bash
    lab01/demo $ vendor/bin/simple-phpunit
    PHPUnit 5.7.26 by Sebastian Bergmann and contributors.

    Testing Project Test Suite
    .................................................                 49 / 49 (100%)

    Time: 27.65 seconds, Memory: 42.00MB

    OK (49 tests, 88 assertions)
    matt@matts-MacBook-Pro demo (master) $

```


## Explore directory `/tests`
Look in the `/tests` directory to see how those tests work. For example Figure \ref{test_new_post} shows a screenshot of the admin new post test in PHPStorm.

![The admin new post test in PHPStorm \label{test_new_post}](./03_figures/app03_test/new_post.png)

## Learn more

Learn more about PHPUnit testing and Symfony by visiting:

- [https://symfony.com/doc/current/testing.html](https://symfony.com/doc/current/testing.html)

