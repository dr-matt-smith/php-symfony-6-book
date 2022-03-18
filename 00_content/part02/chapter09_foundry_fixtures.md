
# Foundry Factories for powerful fixture generation\label{chapter_foundry_fixtures}

## Foundry and FakerPHP

Foundry is a relatively new Symfony library, allowing us to make **Factories** to easily generate 10s or 100s of test data, exploiting Faker features

Learn more at Symfonycasts:

- Foundry

    - [https://symfonycasts.com/screencast/symfony-doctrine/foundry](https://symfonycasts.com/screencast/symfony-doctrine/foundry)

- FakerPHP

    - [https://symfonycasts.com/screencast/symfony-doctrine/foundry-tricks#play](https://symfonycasts.com/screencast/symfony-doctrine/foundry-tricks#play)

## Adding Foundry to our project (project `db08`)

First, we need to add Foundry to our project:

```bash
    $ composer require zenstruck/foundry
```

You should see some new folders appear in `/vendor` from `zenstruck` and `fakerphp`.

## Generate more students with Faker

First let's see how we generate 10 more students using Faker by itself. Update the `load()` method of class `StudentFixtures` to end with this loop

```php
  namespace App\DataFixtures;
  
  ...
  
  // we'll need to refer to the 'Faker' class
  use Mattsmithdev\FakerSmallEnglish\Factory;
  
  class StudentFixtures extends Fixture implements DependentFixtureInterface
  {
       public function load(ObjectManager $manager): void
      {
          // create named references
          $campusBlanchardstown = $this->getReference('CAMPUS_BLANCH');
          $campusTallaght = $this->getReference('CAMPUS_TALLAGHT');
          $campusCity = $this->getReference('CAMPUS_CITY');
  
          ... as before ..
  
          // -- make 10 students with Faker
          $faker = Factory::create();
          $numStudents = 10;
          for ($i=0; $i < $numStudents; $i++) {
              $student = new Student();
  
              $firstName = $faker->firstNameMale;
              $surname = $faker->lastName;
  
              $student->setFirstName($firstName);
              $student->setSurname($surname);
              $student->setCampus($campusBlanchardstown);
  
              $manager->persist($student);
          }
  
          $manager->flush();
      }
```

So we can see that with Faker, we can loop through and create more `Student` objects to add to the database, with random, plausible data for names.

If we load fixtures with `symfony console do:fi:lo` (or `symfony console doctrine:fixtures:load`) and click the `students` link, we'll see 13 students in total, 3-13 being the ones created by Faker:


```bash
    $ symfony console do:fi:lo    
        
     Careful, database "web5" will be purged. Do you want to continue? (yes/no) [no]:
     > y
    
       > purging database
       > loading App\DataFixtures\CampusFixtures
       > loading App\DataFixtures\StudentFixtures    
```



Figure \ref{faker_students} shows a screenshot of a web browser listing the students.

![The Faker-generated students seen in web browser. \label{faker_students}](./03_figures/part02/8_fakerStudents.png){width=50%}

## View campus for students

Let's edit the Twig template listing the students, adding another column to see the campus for each student. Edit the loop in `templates/student/index.html.twig` to look as follows, and

```twig
  {% extends 'base.html.twig' %}

  {% block title %}Student index{% endblock %}

  {% block body %}
    <h1>Student index</h1>

    <table class="table">
        <thead>
            <tr>
                <th>Id</th>
                <th>FirstName</th>
                <th>Surname</th>
                <th>Campus</th> <!-- **** add Campus column heading ****  -->
                <th>actions</th>
            </tr>
        </thead>
        <tbody>
        {% for student in students %}
            <tr>
                <td>{{ student.id }}</td>
                <td>{{ student.firstName }}</td>
                <td>{{ student.surname }}</td>
                <td>{{ student.campus.name }}</td> <!-- ****  output linked campus name ****  -->
                <td>
                    <a href="{{ path('student_show', {'id': student.id}) }}">show</a>
                    <a href="{{ path('student_edit', {'id': student.id}) }}">edit</a>
                </td>
            </tr>
```

Figure \ref{student_campuses} shows a screenshot of a web browser listing the students with the extra campus column.

![Students showing campus. \label{student_campuses}](./03_figures/part02/9_students_withCampus.png){width=75%}


However, using Foundry we can do things easier, and with more sophistication ...

## Creating a Foundry Factory

At the core of Foundry is the **Factory**. So we need to first generate a `Student` Factory. We make a factory with the Symfony console command `make:factory`, then we need to choose which **Entity** class we wish to make the factory for (`Student` in this case):

```bash
  % symfony console make:factory
   // Note: pass --test if you want to generate factories in your tests/ directory  
   // Note: pass --all-fields if you want to generate default values for all fields, not only required fields
  
   Entity class to create a factory for:
    [0] App\Entity\Campus
    [1] App\Entity\Student
   > 1
  
   created: src/Factory/StudentFactory.ph
       -- Success! 
   Next: Open your new factory and set default values/states.
   Find the documentation at https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
```

You should now see a new class `src/Factory/StudentFactory` in your project. While this lists several methods, the important one is `getDefaults()`, which we can see has been made to use FakerPHP to add text for string properties `firstName` and `surname`.

```php
  namespace App\Factory;
  
  ...
  
  final class StudentFactory extends ModelFactory
  {
      ...
      protected function getDefaults(): array
      {
          return [
              'firstName' => self::faker()->text(),
              'surname' => self::faker()->text(),
          ];
      }
```

We can now replace all those lines of code using Faker in the `StudentFixtures` class with just one line (and one `use` statement):


```php
  namespace App\DataFixtures;
  
  ...
  
  use App\Factory\StudentFactory;
  
  class StudentFixtures extends Fixture implements DependentFixtureInterface
  {
       public function load(ObjectManager $manager): void
      {
          // create named references
          $campusBlanchardstown = $this->getReference('CAMPUS_BLANCH');
          $campusTallaght = $this->getReference('CAMPUS_TALLAGHT');
          $campusCity = $this->getReference('CAMPUS_CITY');
  
          ... as before ..
  
          // -- make 10 students with Foundry
          StudentFactory::new()->createMany(10);
  
          $manager->flush();
      }
```

So alredy we can see how using Foundry Factories is making our fixture classes is much simpler. However, if we load fixtures then list students in the browser we get a NULL error (since there is no `Campus` link to these students). First, let's edit our Twig template, to avoid NULL errors - we'll only try to display the `campus.name` string if the `campus` property is not null. So edit `templates/student/index.html.twig` as follows:

```twig
        {% for student in students %}
            <tr>
                <td>{{ student.id }}</td>
                <td>{{ student.firstName }}</td>
                <td>{{ student.surname }}</td>
                <td> <!-- ****** output linked campus name ---- -->
                    {% if not student.campus is null %}
                        {{ student.campus.name }}
                    {% endif %}
                </td> 
```

Now if we list students in the browser (Figure \ref{students_foundry1}) we can see that, although there is random text for the names, they are not plausible names. 

![Students (with very long random word names) generated by Foundry factory. \label{students_foundry1}](./03_figures/part02/10_students_textName.png){width=75%}


That's easily fixed - we just need to edit the `getDefaults()` of class `src/Factory/StudentFactory`, to use the Faker methods `firstNameMale()` and `lastName()` for those properties:

```php
  namespace App\Factory;
  
  ...
  
  final class StudentFactory extends ModelFactory
  {
      ...
      protected function getDefaults(): array
      {
          return [
            'firstName' => self::faker()->firstNameMale(),
            'surname' => self::faker()->lastName(),          
          ];
      }
```


Now if we re-load fixtures, then list students in the browser (Figure \ref{students_foundry2}) we can see that we have believable values for the names again.

![Students with better names generated by Foundry factory. \label{students_foundry2}](./03_figures/part02/11_students_fakerName.png){width=75%}

## Making Foundry choose one campus

In our `StudentFixtures` class, we have already got a reference to each campus. So we can tell the `StudentFactory` to always set the `campus` property to, say, the Blanchardstown campus. We do this by passing key-value array second argument as follows:

```php
  StudentFactory::new()->createMany(10,
      ['campus' => $campusBlanchardstown]
  );
```

This is fine, if we want **all** of our generated `Student` objects to be linked to a single campus.



## Getting Foundry to choose randomly from existing Campuses

One way to get Foundry to set the campus for each generated Student to a random campus, is to the use the Faker method `randomElement(<array>)`. The code would look like this:

```bash
       StudentFactory::new()->createMany(10,
            function() {
                $campusBlanchardstown = $this->getReference('CAMPUS_BLANCH');
                $campusTallaght = $this->getReference('CAMPUS_TALLAGHT');
                $campusCity = $this->getReference('CAMPUS_CITY');

                $campusArray = [$campusBlanchardstown, $campusTallaght, $campusCity];

                $faker = Factory::create();
                $randomCampus = $faker->randomElement($campusArray);
                return ['campus' => $randomCampus];
            }
        );
```

Wow - that's complicated. Part of the reason it's complicated is the need for a function to be used, to ensure each separate Factory-generated student gets a newly created value for campus. But since we have this **anonymous** function, we have to retrieve the `Campus` referenced objects, and create the Faker object, all inside this function that is an argument to the Factory's `createMany(...)` method.

let's find a better way ...

## Relating multiple Foundry Factories

Foundry factories work well with each other!. So to greatly simplify our code we need to create a `Campus` factory, even though we are happy to create 3 specific campuses in the `CampusFixtures` class.

Do the following:

1. Generate a `Campus` factory

   - we do this at the command line with console command: `symfony console make:factory`, then choose the `Campus` entity
   - we do **NOT** need to make any changes to this generated factory, since we won't be using it to generate any randomly populated `Campus` objects!

2. In class `StudentFixtures` add a `use` statement for our new `CampusFactory` class

3. In class `StudentFixtures` replace the code in the `load(...)` method of our with the following:

```php
    StudentFactory::new()->createMany(10,
        function() {
            return ['campus' => CampusFactory::random()];
        }
    );
```

We can see that we still have to pass an anonymous function as the second argument to the `createMany(...)` method. However, we can also see that the code in this function is now just 1 line. This is because we can reference our `CampusFactory` class, whose `random()` method means it will randomly choose one of the `Campus` objects returned by the Doctrine ORM manager - i.e. one of the 3 objects created in class `CampusFixtures`.


If we remove the 3 hard-coded `Student` objects from our code, we can reduce our entire `StudentFixtures` class to just a few lines - stating the dependency to generate the `CampusFixtures` first, and then to generate 10 students, each linked to a random `Campus`:

```php
    namespace App\DataFixtures;
    
    ...
    
    class StudentFixtures extends Fixture
    {
        public function load(ObjectManager $manager): void
        {
            StudentFactory::new()->createMany(10,
                function() {
                    return ['campus' => CampusFactory::random()];
                }
            );
    
            $manager->flush();
        }
    
        public function getDependencies()
        {
            return [
                CampusFixtures::class,
            ];
        }
    }
```


Now if we re-load fixtures, then list students in the browser (Figure \ref{students_foundry3}) we can see that we have nicely generated students, each related randomly to one of our 3 specific campuses.

![All students generated with random campuses. \label{students_foundry3}](./03_figures/part02/12_randomCampuses.png){width=75%}


## A single fixtures class (project `db09`)

We can actually **completely get rid of** the `CampusFixtures` class altogether!

We can use the `createOne(...)` method of the `CampusFactory` class to create our 3 campuses - with the specific values for the `Campus` names that we wish.

Then, as above, the `createMany(...)` method of the `StudentFactory` class to create our 10 random students, related randomly to one of the 3 campuses.

So do the following:

1. delete class `CampusFixtures`

2. replace the code for class `StudentFixtures` to be just the following:

```php
    namespace App\DataFixtures;
    
    use Doctrine\Bundle\FixturesBundle\Fixture;
    use Doctrine\Persistence\ObjectManager;
    
    use App\Entity\Student;
    use App\Entity\Campus;
    
    use App\Factory\StudentFactory;
    use App\Factory\CampusFactory;
    
    class StudentFixtures extends Fixture
    {
        public function load(ObjectManager $manager): void
        {
            CampusFactory::createOne(['name' => 'Blanchardstown']);
            CampusFactory::createOne(['name' => 'Tallaght']);
            CampusFactory::createOne(['name' => 'City']);
    
            StudentFactory::new()->createMany(10,
                function() {
                    return ['campus' => CampusFactory::random()];
                }
            );
    
            $manager->flush();
        }
    }
```

Since we are not relating fixtures between different classes anymore, we don't need any `getDependencies()` method or use of the `DependentFixtureInterface`.

- perhaps we need a better name for our single, fixtures class - perhaps back to `AppFixtures` - so we could use the default class in future, to make use of our Foundry Factory classes.

So you can begin to see the power of Foundry for creating fixture data in your projects ...

