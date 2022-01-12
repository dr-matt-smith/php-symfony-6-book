

# Relating object in different Fixture classes\label{chapter_related_fixtures}

## Remember this for later

Alhtough, relating entities is covered later in the book, relating fixtures is here, as part of this fixtrues chapter.

So, although you may wish to just read this chapter, but leave its implementation until later, do read through and see how easy it is to create related fixtures for objects of different classes.

## Related objects - option 1 - do it all in one Fixture class 

If you need to create fixtures involving related objects of different classes, one solution is to have a single Fixtures class, and create **ALL** your objects in the `load()` method. 

However, if you have 100s of objects this makes a pretty long and messy class.

## Related objects - option 2 - store references to fixture objects (project `db07`)

A better solution involves storing a reference to objects created in one fixture class, than can be used to retrieve those objects for use in another fixture class. 

Let's create a simple, two-class example of `Student` and `Campus` objects, e.g.:

- Student 1 "Matt Smith" is of Campus "Blanchardstown"
- Student 2 "Sinead Murphy" is of Campus "Tallaght"

Since `Campus` comes alphabetically before `Student`, then let's create our 2 `CAmpus` objects and store references to them, in a new fixtures class `CampusFixtures`

### `Category` entity class

Create a class `Campus`  with a single `name` String property.

Use the CLI make tool: `php bin/console ma:en Campus`



### `CampusFixtures` class

Create a class `CampusFixtures` class and create 3 `Campus` objects for "Blanchardstown", "Tallaght" and "City" the usual way.

Use the CLI tools to create your fixtures class: `php bin/console ma:fi CampusFixtures`

```php
<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

use App\Entity\Campus;

class CampusFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $campus1 = new Campus();
        $campus1->setName('Blanchardstown');
        $campus2 = new Campus();
        $campus2->setName('Tallaght');
        $campus3 = new Campus();
        $campus3->setName('City');

        $manager->persist($campus1);
        $manager->persist($campus2);
        $manager->persist($campus3);

        $manager->flush();
    }
}

```

Now we need to also add 2 named **references** to these `Category` objects. It is these that will allow us to retrieve references to these `Campus` obejcts in our `Student` fixtures class:


```php
    public function load(ObjectManager $manager): void
    {
        $campus1 = new Campus();
        $campus1->setName('Blanchardstown');
        $campus2 = new Campus();
        $campus2->setName('Tallaght');
        $campus3 = new Campus();
        $campus3->setName('City');

        $manager->persist($campus1);
        $manager->persist($campus2);
        $manager->persist($campus3);

        $manager->flush();

        // create named references 
        $this->addReference('CAMPUS_BLANCH', $campus1);
        $this->addReference('CAMPUS_TALLAGHWT', $campus2);
        $this->addReference('CAMPUS_CITY', $campus3);
    }
```

## updating our `Student` entity class

We can relate entities through properties of type `relation`. Let's add a `campus` property to `Student` objects, relating each `Student` object to one `Campus` object.

Use the make CLI tool to **add** a new property to the `Student` entity class: `bin/console ma:en Student`. NOTE that both to create a new entity class, and to edit an existing entity class we use the same CLI command `make:entity` or `ma:en`.

We only have to answer a few questions:
- **name** of new property = `campus`
- **data type** of new property = `relation`
- **class** the property is relating Student objects to = `Campus`
- **relationship type** = `ManyToOne` (many Students related to one Campus)
- (once you get to the null-able question you can just keep hitting RETURN to accept all defaults and complete the update of this entity class)

Here is a full summary of the CLI interaction to add this property: 

```bash
 php bin/console ma:en Student

 Your entity already exists! So let's add some new fields!

 New property name (press <return> to stop adding fields):
 > campus

 Field type (enter ? to see all types) [string]:
 > relation

 What class should this entity be related to?:
 > Campus

What type of relationship is this?
 ------------ --------------------------------------------------------------------- 
  Type         Description                                                          
 ------------ --------------------------------------------------------------------- 
  ManyToOne    Each Student relates to (has) one Campus.                            
               Each Campus can relate to (can have) many Student objects            
                                                                                    
  OneToMany    Each Student can relate to (can have) many Campus objects.           
               Each Campus relates to (has) one Student                             
                                                                                    
  ManyToMany   Each Student can relate to (can have) many Campus objects.           
               Each Campus can also relate to (can also have) many Student objects  
                                                                                    
  OneToOne     Each Student relates to (has) exactly one Campus.                    
               Each Campus also relates to (has) exactly one Student.               
 ------------ --------------------------------------------------------------------- 

 Relation type? [ManyToOne, OneToMany, ManyToMany, OneToOne]:
 > ManyToOne

 Is the Student.campus property allowed to be null (nullable)? (yes/no) [yes]:
 > 
```

If you examine the `Student` entity class you'll now see a new property `campus` as follows:

```php
    #[ORM\ManyToOne(targetEntity: Campus::class, inversedBy: 'students')]
    private $campus;
```

There are also get/set accessor methods for this property. 

NOTE: If you look at the `Campus` entity, you'll see that from one of the defaults we accepted, there is now a recipricol array property `students`, so that given a `Campus` object we have an array of all `Student` objects related to it!


### Create and run migration

Since we changed our entity clases, we need to create and run a new migration, to sychronise the DB scheme to match these entity classes:

```bash
    $ php bin/console make:mi
               
      Success! 

    $ php bin/console do:mi:mi
        
         WARNING! You are about to execute a migration in database "web4" that could result in schema changes and data loss. Are you sure you wish to continue? (yes/no) [yes]:
         > y
        
        [notice] Migrating up to DoctrineMigrations\Version20220111214334
        [notice] finished in 170.8ms, used 20M memory, 1 migrations executed, 4 sql queries
```

### `StudentFixtures` class

We can now update our fixtures class for `Student` objects, to relate each new `Student` object to a `Campus` object.

First, we need to add a `use` statement, so that in our `StudentFixtures` class we can refer to `Campus`  objects.

```php
    <?php
    
    namespace App\DataFixtures;
    
    use Doctrine\Bundle\FixturesBundle\Fixture;
    use Doctrine\Persistence\ObjectManager;
    
    use App\Entity\Student;
    use App\Entity\Campus;
```

Next, at the beginning of the `load(...)` method, the first thing we'll do is retrieve the references to the 3 campuses, into suitable named variables:
 
```php
    public function load(ObjectManager $manager): void
    {
        // create named references
        $campusBlanchardstown = $this->getReference('CAMPUS_BLANCH');
        $campusTallaght = $this->getReference('CAMPUS_TALLAGHT');
        $campusCity = $this->getReference('CAMPUS_CITY');
```

We can then go ahead as before, create the `Student` objects, and set their campuses to these `Campus` object references. So we'll set students 1 and 2 to the Blanchardstown campus, and student 3 to the Tallaght campus:

```php
    // create our 3 Student objects
    $s1 = new Student();
    $s1->setFirstName('matt');
    $s1->setSurname('smith');
    $s2 = new Student();
    $s2->setFirstName('joe');
    $s2->setSurname('bloggs');
    $s3 = new Student();
    $s3->setFirstName('joelle');
    $s3->setSurname('murph');

    // set the campus for the students
    $s1->setCampus($campusBlanchardstown);
    $s2->setCampus($campusBlanchardstown);
    $s3->setCampus($campusTallaght);
```

## Dependent Fixtures - order of loading is important!

The `StudentFixtures` class is dependent on the `CampusFixtures` class. So we must declare this dependency so that these fixtures are executed in the correct order.

The Doctrine ORM provides a special interface for delcaring fixture dependencies, so we need to add a `use` statement in the  `StudentFixtures` class as follows:

```php
    use Doctrine\Common\DataFixtures\DependentFixtureInterface;
```

We must declare that class `StudentFixtures` implements this interface:

```php
    class StudentFixtures extends Fixture implements DependentFixtureInterface
```


The interface demands that we implement a method `getDependencies()`. We can so do, stating that class `StudentFixtures`  is dependent on the `CampusFixtures` class:

```php
    public function getDependencies()
    {
        return [
            CampusFixtures::class,
        ];
    }
```


So the complete `StudentFixtures` class looks as follows:

```php
    <?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

use App\Entity\Student;

use Doctrine\Common\DataFixtures\DependentFixtureInterface;


class StudentFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        // create named references
        $campusBlanchardstown = $this->getReference('CAMPUS_BLANCH');
        $campusTallaght = $this->getReference('CAMPUS_TALLAGHT');
        $campusCity = $this->getReference('CAMPUS_CITY');

        // create our 3 Student objects
        $s1 = new Student();
        $s1->setFirstName('matt');
        $s1->setSurname('smith');
        $s2 = new Student();
        $s2->setFirstName('joe');
        $s2->setSurname('bloggs');
        $s3 = new Student();
        $s3->setFirstName('joelle');
        $s3->setSurname('murph');

        // set the campus for the students
        $s1->setCampus($campusBlanchardstown);
        $s2->setCampus($campusBlanchardstown);
        $s3->setCampus($campusTallaght);

        // save these objects to the DB
        $manager->persist($s1);
        $manager->persist($s2);
        $manager->persist($s3);

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




See Figure \ref{related_fixtures} to see the `Product` objects listed from the database, with their linked categories.

![Screenshot of Product fixtures with realted Categories. \label{related_fixtures}](./03_figures/part02/7_related_fixtures.png)


Learn more about Symfony fixtures on the Symfony website:

- [https://symfony.com/bundles/DoctrineFixturesBundle/current/index.html#loading-the-fixture-files-in-order](https://symfony.com/bundles/DoctrineFixturesBundle/current/index.html#loading-the-fixture-files-in-order)
