

# Symfony example - Lab Sheet 1

This lab sheet ensures:

1. You have all software setup for the module on your computer

1. You have run and used the kinds of web applications you'll be creating in the module

![Screenshot showing new Student form with Campus choice dropdown menu](./03_figures/app_crud/crud15_dropDownScreenshot.png){ width=50% }

## Preparation

## Ensure PHP is installed on your computer

You need PHP version 8.1.1 or later.

Open a command line terminal (e.g. the `cmd` application in Windows) and check your PHP version at the command line with:

```bash
    $ php -v
    PHP 8.1.1 (cli) (built: Dec 15 2021 09:54:28) (NTS)
    Copyright (c) 1997-2017 The PHP Group
    Zend Engine v3.1.0, Copyright (c) 1998-2017 Zend Technologies
```

If your version is older than 8.1.1, or you get an error about command not understood, then complete the steps in Appendix \ref{appendix_php}.


## Ensure the `Composer` PHP command line tool is installed on your computer

You need version **2.x** of the Composer command line tool.

Type `composer` in a command line terminal. You should seem something like this:
 
```bash
    $ composer
       ______
      / ____/___  ____ ___  ____  ____  ________  _____
     / /   / __ \/ __ `__ \/ __ \/ __ \/ ___/ _ \/ ___/
    / /___/ /_/ / / / / / / /_/ / /_/ (__  )  __/ /
    \____/\____/_/ /_/ /_/ .___/\____/____/\___/_/
                        /_/
    Composer version 2.1.14 2021-11-30 10:51:43

    // more command summary lines here ....
```

However, if you get an error saying no such application, then install Composer from:

- [https://getcomposer.org/doc/00-intro.md#installation-windows](https://getcomposer.org/doc/00-intro.md#installation-windows) 

See Appendix \ref{cli_tools}.

## Ensure the Git version control utilities are installed

(Git is required for the Symfony command line tool) Run `$ git` at the command line. You should see something like this:

```bash
    $ git
    usage: git [--version] [--help] [-C <path>] [-c <name>=<value>]
               [--exec-path[=<path>]] [--html-path] [--man-path] [--info-path]
    
    These are common Git commands used in various situations:
    
    start a working area (see also: git help tutorial)
       clone      Clone a repository into a new directory
       init       Create an empty Git repository or reinitialize an existing one

```

If not, then visit [https://git-scm.com/download/win](https://git-scm.com/download/win) and run the installer. Then close and open a new terminal window and check Git is working.

## Ensure the `symfony` command line tool is installed on your computer

Having the Symfony command line tool will make things easier (less typing!). Check it at the command line with:

```bash
    $ symfony
    Symfony CLI version 5.2.1 (c) 2017-2022 Symfony SAS (2022-01-22T17:14:23Z - stable)

    Symfony CLI helps developers manage projects, from local code to remote infrastructure
    
    These are common commands .... // more lines here 
```

If you get a suggestion to **update** your version of the Symfony command line tool, say **YES**!

If you get an error saying no such application, then install Symfony from:
              
    - [https://symfony.com/download](https://symfony.com/download) 
    
See Appendix \ref{cli_tools}.


## Ensure the MySQL and SQLite PHP database extensions are enabled (needed for Windows)

If PHP, Composer and Symfony are working, you have PHP setup on your computer sufficient for this module. However, the MySQL and SQLite database extensions may not be setup.

You can either work ahead, hoping it is setup, and fix it if you hit a problem when trying to create a database. Or you can check, and fix it now.

PHP extensions are already installed with PHP, but may not be activated. All we have to do is ensure there is no semi-colon character `;` at the beginning of lines `extension=php_pdo_mysql.dll` and `extension=php_pdo_sqlite.dll`. 

See Appendix \ref{appendix_php} for steps to enable these extensions.

## Open the PHPStorm code editor

On Blanchardstown campus college computers you can open up PHPStorm from the `Jetbrains` folder on the **Desktop**. Run the `phpstorm64.exe` application.

You will need to setup/re-activate your free education Jetbrains licence:

1. Get your free one-year subscription to Jetbrains products using your **TUDublin** university email address

    - `https://www.jetbrains.com/shop/eform/students`


On your own laptop/computer you can download and install the PHPStorm editor for free with your Jetbrains account.

## Download project template and open in a code editor

1. Start your IDE editor (e.g. Notepad++ or PHPStorm)

1. Download from Moodle the ZIP project `crud01.zip`, and unzip to the Desktop.

1. Open a terminal window (either a Terminal application like `cmd`, or open a Terminal window inside your IDE)
 
1. In the terminal `cd` into folder `crud1`


## Run the Symfony web sever

Let's run the web server on our machine (`localhost:8000`) by entering terminal command:

```bash
    $ symfony serve
```
Note, you might get some warnings/info messages about version of PHP etc. - just ignore them!

```bash
    $ symfony serve
      Tailing Web Server log file (/Users/matt/.symfony5/log/nnnnnn.log)
      Tailing PHP-FPM log file (/Users/matt/.symfony5/log/nnnnnnn.log)
       [OK] Web server listening                                                                                              
            The Web server is using PHP FPM 8.1.1                                                                             
            https://127.0.0.1:8000                                                                                            
      [Web Server ] Jan 23 13:22:00 |DEBUG  | PHP    Reloading PHP versions 
      [Web Server ] Jan 23 13:22:00 |DEBUG  | PHP    Using PHP version 8.1.1 (from default version in $PATH) 
      ... lots more status messages ...
      [PHP-FPM    ] Jan 23 13:22:01 |NOTICE | FPM    ready to handle connections 
```

## Visit the home page `localhost:8000`

Open a web browser and visit our website home page at `http://localhost:8000`.

If things are working as expected you should see a default Symfony home page. See Figure \ref{homepage}.

![Default Symfony home page. \label{homepage}](./03_figures/appendices/crud04_homepage.png){ width=75% }

## Test system Symfony requirements

As long as the `symfony` command-line tool has been installed, and we have navigated in the command-line to a Symfony project folder, then we can test the system to see if it has the right version of PHP etc. by running the following command `symfony check:requirements`:

```bash
   $ symfony check:requirements
   
   Symfony Requirements Checker
   ~~~~~~~~~~~~~~~~~~~~~~~~~~~~
   
   > PHP is using the following php.ini file:
   /usr/local/etc/php/8.1/php.ini
   
   > Checking Symfony requirements:
   ...................................
    [OK]                                         
    Your system is ready to run Symfony projects 
   
   Note  The command console can use a different php.ini file
   ~~~~  than the one used by your web server.
         Please check that both the console and the web server
         are using the same PHP version and configuration.
```

If you have any errors identified at this stage, fix them before moving on ...

