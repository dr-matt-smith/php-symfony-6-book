

# Fixtures - setting up a database state \label{chapter_fixtures}



## Initial values for your project database (project `db05`)

Fixtures play two roles:

- inserting initial values into your database (e.g. the first `admin` user)
- setting up the database to a known state for **testing** purposes

Doctrine provides a Symfony fixtures **bundle** that makes things very straightforward.

Learn more about Symfony fixtures at:

- [Symfony website fixtures page](https://symfony.com/doc/master/bundles/DoctrineFixturesBundle/index.html)

## Fixtures SAVE YOU TIME!

I cannot stress how **useful** fixtures are when making many changes to a DB structure - as you'll be likely be doing when first developing Symfony projects.

It should be this easy to resolve mismatches between your code and your database schema:

1. delete the DB (or choose a new DB name in your `.env` file - I just add 1 to the DB number - evote01, evote02 etc.)
2. create the DB: `do:da:cr`
3. delete the **contents** of your `migrations` folder (but not the folder itself)
4. Make a new migration: `ma:mi`
5. run your migration: `do:mi:mi`
6. Load your fixtures: `do:fi:lo`

DONE - your DB is now fully in-synch with your entity classes.

If you do NOT have fixtures,  you'll now waste time entering lots of test data by hand - every time you have to delete and re-create your DB ....

## Installing and registering the fixtures bundle

### Install the bundle
Use Composer to install the bundle in the the `/vendor` directory:

```bash
    composer req orm-fixtures
```

You should now see a new directory created `/src/DataFixtures`. Also, there is a sample fixtures class provided `AppFixtures.php`:

```php
    <?php
    
    namespace App\DataFixtures;
    
    use Doctrine\Bundle\FixturesBundle\Fixture;
    use Doctrine\Persistence\ObjectManager;
    
    class AppFixtures extends Fixture
    {
        public function load(ObjectManager $manager): void
        {
            // $product = new Product();
            // $manager->persist($product);
    
            $manager->flush();
        }
    }
```

Since you'll generally be creating a range of fixture files, named for their content it's a good idea just to delete this file: `/src/DataFixtures/AppFixtures.php`.

## Writing the fixture classes

Fixture classes need to implement the interfaces, `Fixture`.

NOTE: Some fixtures will also require your class to include the  `ContainerAwareInterface`, for when our code also needs to access the container,by implementing the `ContainerAwareInterface`.

Let's create a class to create 3 objects for entity `App\Entity\Student`. The class will be declared in file `/src/DataFixtures/StudentFixtures.php`. However, we can generate the skelton for each fixture class using the CLI `make` tool.
We also need to add a `use` statement so that our class can make use of the `Entity\Student` class.

The **make** feature will create a skeleton fixture class for us. So let's make class `StudentFixtures`:

```bash
    $ symfony console make:fixtures StudentFixtures
    
     created: src/DataFixtures/StudentFixtures.php
    
      Success! 
               
     Next: Open your new fixtures class and start customizing it.
     Load your fixtures by running: symfony console doctrine:fixtures:load
     Docs: https://symfony.com/doc/master/bundles/DoctrineFixturesBundle/index.html
```

Since we are going to be creating instance-objects of class `Student` we need to add a `use` statement:

```php
    ...
    
    use App\Entity\Student;
    
    class StudentFixtures extends Fixture
    {
```

When we use the CLI command `doctrine:fixtures:load` the `load(...)` method of each fixture object is invoked. So now we need to implement the details of our `load(...)` method for our new class `StudentFixtures`.

This method creates objects for the entities we want in our database, and the saves (persists) them to the database. Finally, the `flush()` method is invoked, forcing the database to be updated with all queued new/changed/deleted objects:

In the code below, we create 3 `Student` objects and have them persisted to the database.
```php
    public function load(ObjectManager $manager): void
    {
        $s1 = new Student();
        $s1->setFirstName('matt');
        $s1->setSurname('smith');
        $s2 = new Student();
        $s2->setFirstName('joe');
        $s2->setSurname('bloggs');
        $s3 = new Student();
        $s3->setFirstName('joelle');
        $s3->setSurname('murph');

        $manager->persist($s1);
        $manager->persist($s2);
        $manager->persist($s3);

        $manager->flush();
    }
```

## Loading the fixtures

**WARNING** Fixtures **replace** existing DB contents - so you'll lose any previous data when you load fixtures...

Loading fixtures involves deleting all existing database contents and then creating the data from the fixture classes - so you'll get a warning when loading fixtures. At the CLI type:

```bash
    symfony console doctrine:fixtures:load
```

or the shorter version: `symfony console do:fi:lo`

You should then be asked to enter `y` (for YES) if you want to continue:

```bash
    $ symfony console doctrine:fixtures:load

    Careful, database "web3" will be purged. Do you want to continue? (yes/no) [no]:
    
      > purging database
      > loading App\DataFixtures\StudentFixtures
```


Figure \ref{load_fixtures} shows an example of the CLI output when you load fixtures (in the screenshot it was for initial user data for a login system...)

![Using CLI to load database fixtures. \label{load_fixtures}](./03_figures/database/10_load_fixtures_sm.png)


Alternatively, you could execute an SQL query from the CLI using the `doctrine:query:sql` command:

```bash
    $ symfony console doctrine:query:sql "select * from student"

    /.../db06_fixtures/vendor/doctrine/common/lib/Doctrine/Common/Util/Debug.php:71:
    array (size=3)
      0 =>
        array (size=3)
          'id' => string '13' (length=2)
          'first_name' => string 'matt' (length=4)
          'surname' => string 'smith' (length=5)
      1 =>
        array (size=3)
          'id' => string '14' (length=2)
          'first_name' => string 'joe' (length=3)
          'surname' => string 'bloggs' (length=6)
      2 =>
        array (size=3)
          'id' => string '15' (length=2)
          'first_name' => string 'joelle' (length=6)
          'surname' => string 'murph' (length=5)
```

NOTE:

If you have loaded fixtures several times, or created other records, then the index of the records may NOT begin at 1.

If you need the id's to start at 1, you can delete DB / delete migrations / create DB / create migration / run migration / load fixtures - for a completely fresh dataabase.

## User Faker to generate plausible test data (project `db06`)

For testing purposes the `Faker` library is fantastic for generating plausible, random data.

NOTE: The original PHP Faker was from `fzaninotto/faker`. But this was a muilti-lingual project, being over 3Mb download. So I've created an English-only fork of that project for student use (< 200k). You can read more in the README on Github.

Let's install it and generate some random students in our Fixtures class:

1. use Composer to add the Faker package to our `/vendor/` directory:

    ```bash
        $ composer require mattsmithdev/faker-small-english
   
        Using version ^0.1.0 for mattsmithdev/faker-small-english
        ./composer.json has been updated
        Loading composer repositories with package information
        ...
        Executing script assets:install --symlink --relative public [OK]
    ```

    - you'll now see a `mattsmithdev` folder in `/vendor` containing the Faker classes

2. Add a `uses` statement in our `/src/DataFixtures/LoadStudents.php` class, so that we can make use of the `Faker` class:

    ```php
        use Mattsmithdev\FakerSmallEnglish\Factory;
    ```

3. refactor our  `load()` method in `/src/DataFixtures/LoadStudents.php` to create a Faker 'factory', and loop to generate names for 10 male students, and insert them into the database:

    ```php
        use Mattsmithdev\FakerSmallEnglish\Factory;

        ...

		public function load(ObjectManager $manager): void
		{
			$faker = Factory::create();

			$numStudents = 10;
			for ($i=0; $i < $numStudents; $i++) {
				$firstName = $faker->firstNameMale;
				$surname = $faker->lastName;

				$student = new Student();
				$student->setFirstName($firstName);
				$student->setSurname($surname);

				$manager->persist($student);
			}

			$manager->flush();
		}   
    ```



4. use the CLI Doctrine command to run the fixtures creation method:

    ```bash
        $ symfony console do:fi:lo
        Careful, database will be purged. Do you want to continue y/N ?y
          > purging database
          > loading App\DataFixtures\StudentFixtures
    ```

That's it - you should now have 10 'fake' students in your database.

Figure \ref{fake_students} shows a screenshot of the DB client showing the 10 created 'fake' students.

![Ten fake students inserted into DB. \label{fake_students}](./03_figures/part02/6_fake_students.png)


        
### The Faker projects

Learn more about the `Faker` projects:

- Matt's small version of the library (Github)

  - [https://github.com/dr-matt-smith/faker-small-english](https://github.com/dr-matt-smith/faker-small-english)

- Matt's small version of the library (Packagist)

  - [https://packagist.org/packages/mattsmithdev/faker-small-english](https://packagist.org/packages/mattsmithdev/faker-small-english)

- the FZaninotto library Matt's project is based on
   
  - [https://github.com/fzaninotto/Faker](https://github.com/fzaninotto/Faker)

- FakerPHP - which has replaced FZaninotto Faker library 
  - [https://fakerphp.github.io/](https://fakerphp.github.io/)
  - although it is > 3Mb so still an issue (I'll create a fork using my small Faker library when I have a chance ....)

