# Working with Entity classes



## A `Student` DB-entity class (project `db01`)

Doctrine expects to find entity classes in a directory named `/src/Entity`, and corresponding repository classes in `/src/Repository`.  We already have our  `Student` and `StudentRepository` classes in the right places!

Although we'll have to make some changes to these classes of course.


## Using annotation comments to declare DB mappings

We need to tell Doctrine what table name this entity should map to, and also confirm the data types of each field. We'll do this using annotation comments (although this can be also be declare in separate YAML or XML files if you prefer). We need to add a `use` statement and we define the namespace alias `ORM` to keep our comments simpler.

Our first comment is for the class, stating that it is an ORM entity and mapping it to ORM repository class `StudentRepository`.

```php
    namespace App\Entity;

    use Doctrine\ORM\Mapping as ORM;

    /**
     * @ORM\Entity(repositoryClass="App\Repository\StudentRepository")
     */
    class Student
    {

```

## Declaring types for fields

We now use annotations to declare the types (and if appropriate, lengths) of each field.

```php
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     */
    private $firstName;

    /**
     * @ORM\Column(type="string")
     */
    private $surname;
```

## Validate our annotations

We can now validate these values. This command performs 2 actions, it checks our annotation comments, it also checks whether these match with the structure of the table the database system. Of course, since we haven't yet told Doctrine to create the actual database schema and tables, this second check will fail at this point in time.

```
    $ php bin/console doctrine:schema:validate
```

The output should be something like this (if our comments are valid):

```
    Mapping
    -------
    [OK] The mapping files are correct.

    Database
    --------
    [ERROR] The database schema is not in sync with the current mapping file.
```

## The StudenRepository class (`/src/Repository/StudentRepository`)

We need to change our repository class to be one that works with the Doctrine ORM. Unless we are writing special purpose query methods, all we really need for an ORM repository class is to ensure is subclasses `DoctrineBundle\Repository\ServiceEntityRepository` and its constructor points it to the corresponding entity class.

Change class `StudentRepository` as follows:

- remove all methods
- add `use` statements for:

    ```php
        use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
        use Doctrine\Common\Persistence\ManagerRegistry;      
    ```

- make the class extend class `ServiceEntityRepository`

    ```php
        class StudentRepository extends ServiceEntityRepository
    ```

- add a constructor method:

    ```php
        public function __construct(ManagerRegistry $registry)
        {
            parent::__construct($registry, Student::class);
        }
    ```

So the full listing for `StudentRepository` is now:

```php
    namespace App\Repository;

    use App\Entity\Student;
    use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
        public function __construct(ManagerRegistry $registry)

    class StudentRepository extends ServiceEntityRepository
    {
        public function __construct(ManagerRegistry $registry)
        {
            parent::__construct($registry, Student::class);
        }
    }
```


## Create a migration (a migration `diff` file)

We now will tell Symfony to create the a PHP class to run SQL migration commands required to change the structure of the existing database to match that of our Entity classes:

```bash
    $ php bin/console make:migration

    Success! 
    
    Next: Review the new migration "src/Migrations/Version20180213082441.php"
    Then: Run the migration with php bin/console doctrine:migrations:migrate
    See https://symfony.com/doc/current/bundles/DoctrineMigrationsBundle/index.html
```

NOTE: This is a shorter way of writing the old `doctrine` command: `php bin/console doctrine:migrations:diff`

A migrations SQL file should have been created in `/src/Migrations/...php`:

```php
    namespace DoctrineMigrations;

    use Doctrine\DBAL\Migrations\AbstractMigration;
    use Doctrine\DBAL\Schema\Schema;

    /**
     * Auto-generated Migration: Please modify to your needs!
     */
    class Version20180213082441 extends AbstractMigration
    {
        public function up(Schema $schema)
        {
            // this up() migration is auto-generated, please modify it to your needs
            $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql',
            'Migration can only be executed safely on \'mysql\'.');

            $this->addSql('CREATE TABLE student (id INT AUTO_INCREMENT NOT NULL,
            first_name VARCHAR(255) NOT NULL, surname VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE
```


## Run the migration to make the database structure match the entity class declarations

Run the `migrate` command to execute the created migration class to make the database schema match the structure of your entity classes, and enter `y` when prompted - if you are happy to go ahead and change the database structure:

```bash
    $ php bin/console doctrine:migrations:migrate

        Application Migrations


    WARNING! You are about to execute a database migration that could result in
    schema changes and data lost. Are you sure you wish to continue? (y/n)y
    Migrating up to 20180201223133 from 0

      ++ migrating 20180201223133

         -> CREATE TABLE product (id INT AUTO_INCREMENT NOT NULL,
         description VARCHAR(100) NOT NULL, price NUMERIC(10, 2) DEFAULT NULL,
         PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB

      ++ migrated (0.14s)

      ------------------------

      ++ finished in 0.14s
      ++ 1 migrations executed
      ++ 1 sql queries
```

You can see the results of creating the database schema and creating table(s) to match your ORM entities using a database client such as MySQL Workbench. Figure \ref{db_schema} shows a screenshot of MySQL Workbench showing the database's `student` table to match our `Student` entity class.

![Screenshot MySQL Workbench and generated schema and product table. \label{db_schema}](./03_figures/part02/1_db_schema.png)

## Re-validiate our annotations

We should get 2 "ok"s if we re-validate our schema now:

```
    $ php bin/console doctrine:schema:validate
```

The output should be something like this (if our comments are valid):

```
    Mapping
    -------
    [OK] The mapping files are correct.

    Database
    --------
    [OK] The database schema is in sync with the mapping files.
```

## Generating entities from an existing database

Doctrine allows you to generated entities matching tables in an existing database. Learn about that from the Symfony documentation pages:

- [Symfony docs on inferring entites from existing db tables](https://symfony.com/doc/current/doctrine/reverse_engineering.html)

## Note - use maker to save time (project `db02`)

We could have automatically created our Student entity and StudentRepository classes from scratch, using the `make` package:

```bash
    $ php bin/console make:entity Student
      
       created: src/Entity/Student.php
       created: src/Repository/StudentRepository.php
       
       Entity generated! Now let's add some fields!
       You can always add more fields later manually or by re-running this command.
      
       New property name (press <return> to stop adding fields):
       > 

        Success! 
      
       Next: When you're ready, create a migration with make:migration
    $
```

In the above `<RETURN>` was pressed to not add any fields automatically. The Maker bundle created 2 classes for us:
 
- a Student class `src/Entity/Student.php`, containing just a private `id` property and a public `getId()` method

- and a generic StudentRepository class `src/Repository/StudentRepository.php`

We would then be able to manually add the `firstName` and `surname` properties (and their annotation comments) as we did earlier in the chapter:

```php

    /**
     * @ORM\Column(type="string")
     */
    private $firstName;

    /**
     * @ORM\Column(type="string")
     */
    private $surname;
```

Finally we would have had to generate getters and setters for these 2 fields, and migrate to the database.

## Use maker to create properties, annotations and accessor methods! 

We could automatically create our Student entity and StudentRepository classes from scratch, using the `make` package. It will interactively ask you about fields you wish to create, and add the appropriate annotations and accessor (get/set) methods for you!

If you want to try this, first:

- Delete the entity class: `/src/Entity/Student.php`

- Delete the repository class: `/src/Repository/StudentRepository.php`

Then run the CLI command `make:entity Student`, and at the prompt ask it to create our `firstName` and `surname` text properties (all entities get an auto-incremented `Id` field with us having to ask):

```bash
    $ php bin/console make:entity Student
    
     created: src/Entity/Student.php
     created: src/Repository/StudentRepository.php
     
     Entity generated! Now let's add some fields!
     You can always add more fields later manually or by re-running this command.
    
     New property name (press <return> to stop adding fields):
     > firstName
    
     Field type (enter ? to see all types) [string]:
     > 
    
     Field length [255]:
     > 
    
     Can this field be null in the database (nullable) (yes/no) [no]:
     > surnamne
    
     updated: src/Entity/Student.php
    
     Add another property? Enter the property name (or press <return> to stop adding fields):
     > 
               
      Success! 
    
     Next: When you're ready, create a migration with make:migration    
```

For each property the Maker bundle wants to know 3 things:

- property name (e.g. `firstName` and `surname`)
- property type (default is `string`)
- whether `NULL` can be stored for property

For `string` properties like `firstName` we just need to enter the property name and hit `<RETURN>` for the defaults (`string`, not nullable). For other types of field you can get a list of types by entering `?` at the prompt:. There are quite a few of them:

```bash
     Field type (enter ? to see all types) [string]:
     > ?
    
    Main types
      * string
      * text
      * boolean
      * integer (or smallint, bigint)
      * float
    
    Relationships / Associations
      * relation (a wizard   will help you build the relation)
      * ManyToOne
      * OneToMany
      * ManyToMany
      * OneToOne
    
    Array/Object Types
      * array (or simple_array)
      * json
      * object
      * binary
      * blob
    
    Date/Time Types
      * datetime (or datetime_immutable)
      * datetimetz (or datetimetz_immutable)
      * date (or date_immutable)
      * time (or time_immutable)
      * dateinterval
    
    Other Types
      * json_array
      * decimal
      * guid
```

