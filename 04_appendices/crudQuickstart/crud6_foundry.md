# Adding initial data for campuses and students

## Create factories for `Campus` and `Student`

When developing and testing a web application we are resetting the database many times, and so can save much clicking and typing by coding fixtures - initial database data - to re-populate the database with a few lines of code.

Let's create Foundry **factories** for the `Campus` and `Student` classes we created. 

Do the following:

1. use the Symfony console to make a new factory by typing `symfony console make:factory` (or the abbreviated version `symfony console ma:fa`)

2. you'll be given a list of entity classes which have no existing factory and asked to type the number corresponding to the class you'd like to create a factory for - choose the `Campus` entity class

3. A new factory class `CampusFactory` will be generated for you.

Here is a terminal session I used to do the above:

```bash
    % symfony console ma:fa 
     // Note: pass --test if you want to generate factories in your tests/ directory
    
     // Note: pass --all-fields if you want to generate default values for all fields, not only required fields
    
     Entity class to create a factory for:
      [0] App\Entity\Campus
      [1] App\Entity\Student
     > 0
    
     created: src/Factory/CampusFactory.php
      Success! 
    
     Next: Open your new factory and set default values/states.
     Find the documentation at https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
```

Repeat the above for the `Student` class. So you should now have 2 new factory classes:

- `src/Factory/CampusFactory`
- `src/Factory/StudentFactory`

You might be interested to look inside them, although at this time we'll just make use of them rather than change what they do.


## Update fixture class to create `Campus` objects

The class we are going to add to is the `src/DataFixtures/AppFixtures` class. As we saw earlier seeing the `User` objects being created, we can use the `createOne(...)` method of a fatory, passing an array of parameters to set values for its properties.

First, add 2 new `use` statements at the top of the `AppFixtures` class, so we'll be able to refer to the 2 new factory classes:

```php
    <?php
    
    namespace App\DataFixtures;
    
    use Doctrine\Bundle\FixturesBundle\Fixture;
    use Doctrine\Persistence\ObjectManager;
    
    use App\Factory\UserFactory;
    use App\Factory\MakeFactory;
    use App\Factory\PhoneFactory;
    
    // we want to be able to use our 2 new factories
    use App\Factory\CampusFactory;
    use App\Factory\StudentFactory;
```

Next, let's add to the end of the `load(...)` method statements to create the 3 TUDublin campuses:

```php
    class AppFixtures extends Fixture
    {
        public function load(ObjectManager $manager): void
        {
            ... existing code ...
    
            CampusFactory::createOne(['location' => 'Blanchardstown']);
            CampusFactory::createOne(['location' => 'Tallaght']);
            CampusFactory::createOne(['location' => 'City']);
        }
```

Since we are only populating the single string property `location` for each new `Campus` object, we can create each new object in a single line.

Let's reset the database contents and load the fixtures from this `AppFixtures` class. We use the CLI command `doctrine:fixtures:load` (or `do:fi:lo`). You'll get a warning and have to type `y` to confirm you are **purging** (deleting!) all existing data from the database, and then loading new data from the fixtures classes:

```bash
    $ symfony console do:fi:lo
    
     Careful, database "crud01" will be purged. Do you want to continue? (yes/no) [no]:
     > y
    
       > purging database
       > loading App\DataFixtures\AppFixtures
```

If you run the web server and vist `/campus` now you'll see these 3 `Campus` objects listed in the browser. See Figure \ref{campus_fixtures} for a screenshot of the fixture data created.


![Browser listing fixture `Campus` objects.\label{campus_fixtures}](./03_figures/app_crud/5_fixtures_campus.png){width=100%}


## Update fixture class to create `Student` objects

Creating `Student` initial fixture data is a tiny bit more complex:

- there are more properties for each `Student` object
- the `campus` property is a reference to a `Campus` object

However, Foundry factories work very well with each other, and know about object references etc. Add to the end of the `load(...)` method statements to create the 3 `Student` objects. We'll link one to the `Blanchardstown` campus, and the next two to the `Tallaght` campus:

```php
        CampusFactory::createOne(['location' => 'Blanchardstown']);
        CampusFactory::createOne(['location' => 'Tallaght']);
        CampusFactory::createOne(['location' => 'City']);

        // create Student objects linked to Campus objects
        StudentFactory::createOne([
            'age' => 21,
            'name' => 'Matt Smith',
            'campus' => CampusFactory::find(['location' => 'Blanchardstown']),
        ]);

        StudentFactory::createOne([
            'age' => 96,
            'name' => 'Granny Smith',
            'campus' => CampusFactory::find(['location' => 'Tallaght']),
        ]);

        StudentFactory::createOne([
            'age' => 19,
            'name' => 'Sinead Mullen',
            'campus' => CampusFactory::find(['location' => 'Tallaght']),
        ]);
```

We can see above that we can use the `find(...)` method of the `CampusFactory` class to retrieve a reference to a specific `Campus` object, and use that for setting the `campus` property of the `Student` object being created.

Use the CLI command `doctrine:fixtures:load` (or `do:fi:lo`) to reset the database again, and load all the new objects being created by the factories in our fixtures `load(...)` method.

If you run the web server and vist `/student` now you'll see these 3 `Student` objects listed in the browser. See Figure \ref{student_fixtures} for a screenshot of the fixture data created.


![Browser listing fixures `Student` objects.\label{student_fixtures}](./03_figures/app_crud/5_fixtures_student.png){width=100%}


