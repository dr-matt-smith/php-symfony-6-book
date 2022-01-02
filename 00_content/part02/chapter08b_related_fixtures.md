

# Relating object in different Fixture classes\label{chapter_related_fixtures}

## Related objects - option 1 - do it all in one Fixture class

If you need to create fixtures involving related objects of different classes, one solution is to have a single Fixtures class, and create **ALL** your objects in the `load()` method. 

However, if you have 100s of objects this makes a pretty long and messy class.

## Related objects - option 2 - store references to fixture objects

A better solution involves storing a reference to objects created in one fixture class, than can be used to retrieve those objects for use in another fixture class. 

Let's create a simple, two-class example of `Product` and `Category` objects, e.g.:

- Product 1 "hammer" is of Category "hardware"
- Product 2 "plunger" is of Category "kitchenware"

Since `Category` comes alphabetically before `Product`, then let's create our 2 `Category` objects and store references to them, in a new fixtures class `CategoryFixtures`

## Category class

Create a class `Category`  with a single `name` String property.

## CategoryFixtures class

Create a class `CategoryFixtures`  and create 2 `Category` objects for "hardware" and "kitchenware" the usual way:

```php
<?php
namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Category;

class CategoryFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $cat1 = new Category();
        $cat1->setName('hardware');
        $manager->persist($cat1);

        $cat2 = new Category();
        $cat2->setName('kitchenware');
        $manager->persist($cat2);

        $manager->flush();
    }
}
```

Now we need to also add 2 named references to these `Category` objects so we can retrieve references to them in our `Product` fixtures class:


```php
<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Category;

class CategoryFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $cat1 = new Category();
        $cat1->setName('hardware');
        $manager->persist($cat1);

        $cat2 = new Category();
        $cat2->setName('kitchenware');
        $manager->persist($cat2);

        $manager->flush();
        
        // create named references 
        $this->addReference('CATEGORY_HARDWARE', $cat1);
        $this->addReference('CATEGORY_KITCHENWARE', $cat2);
    }
}
```

## Product class

Create a class `Product`  with properties:
 
- `name` String
- `category` ManyToOne relation to class `Category`

## ProductFixtures class

Create a class `ProductFixtures`  and create 2 `Product` objects for "hammer" and "plunger" the usual way:

```php
<?php

namespace App\DataFixtures;

use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ProductFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $product1 = new Product();
        $product1->setName('hammer');
        $manager->persist($product1);

        $product2 = new Product();
        $product2->setName('plunger');
        $manager->persist($product1);

        $manager->flush();
    }
}
```


Now we need to also create 2 `Category` objects by getting the 2 named references:

```php
<?php

namespace App\DataFixtures;

use App\Entity\Product;
use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ProductFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {

        // create named references
        $cat1 = $this->getReference('CATEGORY_HARDWARE');
        $cat2 = $this->getReference('CATEGORY_KITCHENWARE');

        $product1 = new Product();
        $product1->setName('hammer');
        $product1->setCategory($cat1);
        $manager->persist($product1);

        $product2 = new Product();
        $product2->setName('plunger');
        $product2->setCategory($cat2);
        $manager->persist($product2);

        $manager->flush();
    }
}
```

See Figure \ref{related_fixtures} to see the `Product` objects listed from the database, with their linked categories.

![Screenshot of Product fixtures with realted Categories. \label{related_fixtures}](./03_figures/part02/7_related_fixtures.png)


# Alternative approach using the  DependentFixturesInterface

learn more at:

- https://latteandcode.medium.com/symfony-improving-your-tests-with-doctrinefixturesbundle-1a37b704ac05

