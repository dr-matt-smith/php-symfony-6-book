

# Fixtures - setting up a database state \label{chapter_fixtures}



## Initial values for your project database (project `db05`)

Fixtures play two roles:

- inserting initial values into your database (e.g. the first `admin` user)
- setting up the database to a known state for **testing** purposes

Doctrine provides a Symfony fixtures **bundle** that makes things very straightforward.

Learn more about Symfony fixtures at:

- [Symfony website fixtures page](https://symfony.com/doc/master/bundles/DoctrineFixturesBundle/index.html)

## Installing and registering the fixtures bundle

### Install the bundle
Use Composer to install the bunder in the the `/vendor` directory:

```bash
    composer req orm-fixtures
```

You should now see a new directory created `/src/DataFixtures`. Also there is a sample fixtures class provided `AppFixtures`:

```php
    <?php
    
    namespace App\DataFixtures;
    
    use Doctrine\Bundle\FixturesBundle\Fixture;
    use Doctrine\Common\Persistence\ObjectManager;
    
    class AppFixtures extends Fixture
    {
        public function load(ObjectManager $manager)
        {
            // $product = new Product();
            // $manager->persist($product);
    
            $manager->flush();
        }
    }

```

## Writing the fixture classes

We need to locate our fixtures in our `/src` directory, inside a `/DataFixtures` directory. The path for our data fixtures classes should be `/src/DataFixtures/`.

Fixture classes need to implement the interfaces, `Fixture`.

NOTE: Some fixtures will also require your class to include the  `ContainerAwareInterface`, for when our code also needs to access the container,by implementing the `ContainerAwareInterface`.

Let's write a class to create 3 objects for entity `App\Entity\Student. The class will be declared in file `/src/DataFixtures/StudentFixtures.php`. Make a copy of the provided `AppFixtures` class naming the copy `StudentFixtures`, and change the class name inside the code.

We also need to add a `use` statement so that our class can make use of the `Entity\Student` class.

The **make** feature will create a skeleton fixture class for us. So let's make class `StudentFixtures`:

```bash
    $ php bin/console make:fixtures StudentFixtures
    
     created: src/DataFixtures/StudentFixtures.php
    
      Success! 
               
     Next: Open your new fixtures class and start customizing it.
     Load your fixtures by running: php bin/console doctrine:fixtures:load
     Docs: https://symfony.com/doc/master/bundles/DoctrineFixturesBundle/index.html
```

Since we are going to be creating instance-objects of class `Student` we need to add a `use` statement:

```php
    ...
    
    use App\Entity\Student;
    
    class StudentFixtures extends Fixture
    {
```


Now we need to implement the details of our `load(...)` method, that gets invoked when we are loading fixtures from the CLI. This method creates objects for the entities we want in our database, and the saves (persists) them to the database. Finally the `flush()` method is invoked, forcing the database to be updated with all queued new/changed/deleted objects:

In the code below, we create 3 `Student` objects and have them persisted to the database.
```php
    public function load(ObjectManager $manager)
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
    php bin/console doctrine:fixtures:load
```

You should then be asked to enter `y` (for YES) if you want to continue:

```bash
    $ php bin/console doctrine:fixtures:load

    Careful, database will be purged. Do you want to continue y/N ?y
      > purging database
      > loading App\DataFixtures\LoadStudents

```


Figure \ref{load_fixtures} shows an example of the CLI output when you load fixtures (in the screenshot it was for initial user data for a login system...)

![Using CLI to load database fixtures. \label{load_fixtures}](./03_figures/database/10_load_fixtures_sm.png)


Alternatively, you could execute an SQL query from the CLI using the `doctrine:query:sql` command:

```bash
    $ php bin/console doctrine:query:sql "select * from student"

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

## User Faker to generate plausible test data (project `db06`)

For testing purposes the `Faker` library is fantastic for generating plausible, random data.

Let's install it and generate some random students in our Fixtures class:

1. use Composer to add the Faker package to our `/vendor/` directory:

    ```bash
        $ composer req fzaninotto/faker
   
        Using version ^1.7 for fzaninotto/faker
        ./composer.json has been updated
        Loading composer repositories with package information
        ...
        Executing script assets:install --symlink --relative public [OK]
    ```

1. Add a `uses` statement in our `/src/DataFixtures/LoadStudents.php` class, so that we can make use of the `Faker` class:

```php
    use Faker\Factory;

```
2. refactor our  `load()` method in `/src/DataFixtures/LoadStudents.php` to create a Faker 'factory', and loop to generate names for 10 male students, and insert them into the database:

    ```php
        public function load(ObjectManager $manager) {
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
3. use the CLI Doctrine command to run the fixtures creation method:

    ```bash
        $ php bin/console doctrine:fixtures:load
        Careful, database will be purged. Do you want to continue y/N ?y
          > purging database
          > loading App\DataFixtures\LoadStudents
    ```

That's it - you should now have 10 'fake' students in your database.

Figure \ref{fake_students} shows a screenshot of the DB client showing the 10 created 'fake' students.

![Ten fake students inserted into DB. \label{fake_students}](./03_figures/part02/6_fake_students.png)

Learn more about the `Faker` class at its Github project page:

- [https://github.com/fzaninotto/Faker](https://github.com/fzaninotto/Faker)


## Foundry and FakerPHP

Learn more about fixtures packages at Symfonycasts:

- Foundry

  - [https://symfonycasts.com/screencast/symfony-doctrine/foundry](https://symfonycasts.com/screencast/symfony-doctrine/foundry)

- FakerPHP

   - [https://symfonycasts.com/screencast/symfony-doctrine/foundry-tricks#play](https://symfonycasts.com/screencast/symfony-doctrine/foundry-tricks#play)


