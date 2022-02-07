
# The Composer command line tool\label{cli_tools}

## Preparation

ensure you have PHP working correctly before attempting to install the Composer CLI application.


## Composer
Composer is a **fantastic** PHP tool for managing project dependencies (the libraries and class packages used by OO PHP projects).

The Composer tool is actually a **PHAR** (PHP Archive) - i.e. a PHP application packaged into a single file. So ensure you have PHP installed and in your environment **path** before attempting to install or use Composer.

1. after installation ensure that Composer is always up-to-date by running:

    ```bash
        composer self-update
    ```

2. enable the PDO options for MySQL and SQLite (see Appendix \ref{appendix_php} for how to do this by editing ther `c:\php\php.ini` file ...)





### Windows Composer install

Get the latest version of Composer from

- [getcomposer.org](https://getcomposer.org/)

- run the provided **Composer-Setup.exe** installer (just accept all the default options - do NOT tick the developer mode)

    -- [https://getcomposer.org/doc/00-intro.md#installation-windows](https://getcomposer.org/doc/00-intro.md#installation-windows)


### Linux / MacOS Composer install

Follow the instructions to download Composer, and also to install it **globally** - so it will work wherever you have `cd`d to.
