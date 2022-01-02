

# Quick setup for new 'blog' project \label{appendix_new_project}

## Create a new project, e.g. 'blog'

Use the Symfony command line installer (if working for you) to create a new project named 'blog' (or whatever you want!)

```bash
    $ symfony new blog
```

Or use Composer:

```bash
    $ composer create-project symfony/framework-standard-edition blog
```

Read more at:

- [Symfony create project reference](http://symfony.com/doc/current/best_practices/creating-the-project.html)

## Set up your localhost browser shortcut to for `app_dev.php`

Set your web browser shortcut to the `app_dev.php`, i.e.:

```
    http://localhost:8000/app_dev.php
```

## Add `run` shortcut to your Composer.json scripts

Make life easier - add a "run" Composer.json script shortcut to run web server from command line:

```json
    "scripts": {
        "run":"php bin/console server:run",
        ...
```

## Change directories and run the app

Change to new project directory and run the app

```bash
    /~user/$ cd blog
    /~user/blog/$ composer run
```

Now visit: `http://localhost:8000/app_dev.php` in your browser to see the welcome page

## Remove default content

If you want a **completely blank** Symfony project to work with, then delete the following:

```
    /src/AppBundle/Controller/DefaultController.php
    /app/Resources/views/default/
    /app/Resources/views/base.html.twig
```

Now you have no controllers or Twig templates, and can start from a clean slate...
