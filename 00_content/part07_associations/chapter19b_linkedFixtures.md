
# Linked object fixtures

## Linking objects when loading fixtures

Let's use Foundry to easily create fixtures linking objects.

First, we need to add Foundry to our project:

```bash
    $ composer require zenstruck/foundry
```

## Creating factories for each entity

Use the Symfony maker feature to make factories for both entites:

```bash
    $ symfony console make:factory

     Entity class to create a factory for:
      [0] App\Entity\Category
      [1] App\Entity\Product
     > 0
```

## Updating `AppFixtures` to create `Category` objects with `CategoryFactor`

Let's update class `/src/DataFixtures/AppFixtures.php` to create the 2 `Category` fixture objects using  `CategoryFactor`

```php
    use App\Factory\CategoryFactory;
    use App\Factory\ProductFactory;

    class AppFixtures extends Fixture
    {
        public function load(ObjectManager $manager): void
        {
            $catSmall = CategoryFactory::createOne([
                'name' => 'small items'
            ]);

            $catLarge = CategoryFactory::createOne([
                'name' => 'large items'
            ]);
        }
    }
```

## Creating several `Product` objects with `ProductFactory`

Once we've created objects for the 'one' side of an association (Many Products to One Category), we can now create the fixture objects for the 'Many' side, linking them to the already created objects.

Add the following lines to `AppFixtures` to create 3 Product objects, 2 linked to the small items category, and the last (ladder) linked to the large items category.


```php
        $catSmall = CategoryFactory::createOne(['name' => 'small items']);

        $catLarge = CategoryFactory::createOne(['name' => 'large items']);

        ProductFactory::createOne([
            'description' => 'hammer',
            'image' => 'hammer.png',
            'price' => 9.99,
            'category' => $catSmall
        ]);
        ProductFactory::createOne([
            'description' => 'bag of nails',
            'image' => 'nails.png',
            'price' => 0.99,
            'category' => $catSmall
        ]);

        ProductFactory::createOne([
            'description' => 'ladder',
            'image' => 'ladder.png',
            'price' => 19.99,
            'category' => $catLarge
        ]);
```
