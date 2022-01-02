
# Doctrine the ORM


<!--
hello mum !
-->


## What is an ORM?

The acronym ORM stands for:

- O: Object
- R: Relational
- M: Mapping

In a nutshell projects using an ORM mean we write code relating to collections of related **objects**, without having to worry about the way the data in those objects is actually represented and stored via a database or disk filing system or whatever. This is an example of 'abstraction' - adding a 'layer' between one software component and another. DBAL is the term used for separating the database interactions completed from other software components. DBAL stands for:

- DataBase
- Abstraction
- Layer

With ORMs we can interactive (CRUD^[CRUD = Create-Read-Update-Delete])
with persistent object collections either using methods of the object repositories (e.g. `findAll()`, `findOneById()`, `delete()` etc.), or using SQL-lite languages. For example Symfony uses the `Doctrine` ORM system, and that offers `DQL`, the Doctrine Query Language.

You can read more about ORMs and Symfony at:

- [Doctrine project's ORM page](http://www.doctrine-project.org/projects/orm.html)
- [Wikipedia's ORM page](https://en.wikipedia.org/wiki/Object-relational_mapping)
- [Symfony's Doctrine help pages](https://symfony.com/doc/current/doctrine.html)

## Setting the database connection URL for MySQL

NOTE: This chapter assumes you are starting from the Student basic project from the end of the last chapter...

Edit file `.env` to change the default database URL to one that will connect to MySQL server running at port 3306, with username `root` and password `pass`, and working with databse schema `web3` (or whatever **you want to name your database ...**)

So change this line in `.env` from:

```bash
    DATABASE_URL=mysql://db_user:db_password@127.0.0.1:3306/db_name
```

to

```bash
    DATABASE_URL=mysql://root:pass@127.0.0.1:3306/web3
```

NOTE: If you prefer to parametize the database connection, use environment variables and then `${VAR}` in your URL:

```bash
    DB_USER=root
    DB_PASSWORD=pass
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_NAME=web3
    DATABASE_URL=mysql://${DB_USER}:${DB_PASSWORD}@${DB_HOST}:${DB_PORT}/${DB_NAME}
```


## Setting the database connection URL for SQLite

If you want a non-MySQL database setup for now, then just use the basic SQLite setup:

So change this line in `.env` from:

```bash
    DATABASE_URL=mysql://db_user:db_password@127.0.0.1:3306/db_name
```

to

```bash
    DATABASE_URL=sqlite:///%kernel.project_dir%/var/data.db
```

This will work with SQLite database file `data.db` in directory `/var`.

## Quick start
Once you've learnt how to work with Entity classes and Doctrine, these are the 3 commands you need to know (executed from the CLI console `php bin/console ...`):

1. `doctrine:database:create`
1. `doctrine:migrations:diff`
1. `doctrine:migrations:migrate` (or possibly `doctrine:schema:update --force`)
1. `doctrine:schema:validate`
1. `doctrine:fixtures:load`
1. `doctrine:query:sql`

This should make sense by the time you've reached the end of this database introduction.

## Make your database

We can now use the settings in the `.env` file to connect to the MySQL server and create our database schema:

```bash
    $ php bin/console doctrine:database:create
```
