

# Software tools\label{appendix_software_setup}

NOTE: All the following should already available on the college computers.

## PHPStorm editor

Ensure you have your free education Jetbrains licence from:

- [Students form: https://www.jetbrains.com/shop/eform/students](https://www.jetbrains.com/shop/eform/students) (ensure you use your ITB student email address)

Downdload and install PHPStorm from:

- [https://www.jetbrains.com/phpstorm/download/](https://www.jetbrains.com/phpstorm/download/)

To save lots of typing, try to install the following useful PHPStorm plugins:

- Twig
- Symfony
- Annotations

## MySQL Server

You will need a MySQL-compatible database **server**. Simplest is the free MySQL Community Server, but others like MariaDB should be fine
(although you may need to add a fix for things like JSON strings, relating to security roles in Symfony ... fiddly but easy - ask Matt if you are meet any JSON string DB errors ...)

While you can work with SQLite and other database management systems, many ITB modules use MySQL Server for database work, and it's fine, so that's what we'll use (and, of course, it is already installed on the ITB windows computers ...)

Download and install MySQL Community Server from:

- [https://dev.mysql.com/downloads/mysql/](https://dev.mysql.com/downloads/mysql/)

##  Temporary swap to use SQLite

While you should get MySQL working on your system, to get started quickly with Symfony when you have MySL issues you can add a line to the end of your `.env` configuration file to temporarily use an SQLite database file in the `/var` directory.

To do this, add this extra line to the end of  your `.env` configuration file:

```
    DB_USER=root
    DB_PASSWORD=passpass
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_NAME=db01
    DATABASE_URL=mysql://${DB_USER}:${DB_PASSWORD}@${DB_HOST}:${DB_PORT}/${DB_NAME}
     
    DATABASE_URL=sqlite:///%kernel.project_dir%/var/data.db
```

Since the `sqqlite` URL value comes AFTER the `mysql` value, it will replace the MySQL URL value with this SQLIte one for now ....

To completely reset an SQLite database you can:

1. Delete the entire `/var` folder

2. Delete the **contents** of the `/migrations` folder (but not the folder itself)



## MySQL Workbench

Workbench is a MySQL database **client** - you will also need a MySQL **server** for the client to connect to (see above).

Many ITB modules use MySQLWorkbench as the client for database work, and it's fine, so that's what we'll use (and, of course, it is already installed on the ITB windows computers ...)

Download and install MySQL Workbench from:

- [https://dev.mysql.com/downloads/workbench/](https://dev.mysql.com/downloads/workbench/)

## Git

Git is a fantastic (and free!) DVCS - Distributed Version Control System. It has free installers for Windows, Mac, Linus etc.

Check is Git in installed on your computer by typing `git` at the CLI terminal:

```bash
    > git
    usage: git [--version] [--help] [-C <path>] [-c name=value]
               [--exec-path[=<path>]] [--html-path] [--man-path] [--info-path]
               [-p | --paginate | --no-pager] [--no-replace-objects] [--bare]
               [--git-dir=<path>] [--work-tree=<path>] [--namespace=<name>]
               <command> [<args>]

    These are common Git commands used in various situations:

    start a working area (see also: git help tutorial)
       clone      Clone a repository into a new directory
       init       Create an empty Git repository or reinitialize an existing one

    ...

    collaborate (see also: git help workflows)
       fetch      Download objects and refs from another repository
       pull       Fetch from and integrate with another repository or a local branch
       push       Update remote refs along with associated objects

    'git help -a' and 'git help -g' list available subcommands and some
    concept guides. See 'git help <command>' or 'git help <concept>'
    to read about a specific subcommand or concept.

    >
```

If you don't see a list of **Git** commands like the above, then you need to install Git on your computer.

## Git Windows installation

Visit this page to run the Windows Git installer.

- [https://git-scm.com/downloads](https://git-scm.com/downloads)

NOTE: Do **not** use a GUI-git client. Do all your Git work at the command line. It's the best way to learn, and it means you can work with Git on other computers, for remote terminal sessions (e.g. to work on remote web servers) and so on.

