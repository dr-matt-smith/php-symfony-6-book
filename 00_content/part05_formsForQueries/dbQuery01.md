# Custom database queries

## Search for exact property value (project `query01`)

Let's create a simple database schema for hardware products, and then write some forms to query this database.

## Preparation - new project with fixtures package

Create a new project:

```bash
     symfony new --webapp query01
```

Now `cd` into the project in a terminal, and use `make:entity` to create a new Entity class `Product`:


```bash
     symfony console make:entity Product
```

Add the following properties to this new entity:

- description: String
- price: Float
- category: String

Use `make:crud` to generate the CRUD pages for Product entities:

```bash
     symfony console make:crud Product
```

## Remove encore entry points in base Twig template

If you are not using WebPack encore, remove the `encore_entry_` lines inside the `stylesheets` and `javascripts` blocks in the base template (`/templates/base.html.twig`). So these blocks will be empty in this parent template file:

```twig
    {% block stylesheets %}
    {% endblock %}

    {% block javascripts %}
    {% endblock %}
```

## Create a new database and migration

Edit your `.env` for a new MySQL database, and make a migration, and run the migration to create your DB schema for this entity.Category

E.g.
```bash
     symfony console doctrine:database:create
     symfony console make:migration
     symfony console doctrine:migrations:migrate
```

or the 2-letter abbreviations:
```bash
     symfony console do:da:cr
     symfony console ma:mi
     symfony console do:mi:mi
```


## Fixtures

Add the fixtures package:

```bash
     composer req orm-fixtures
```

Write fixtures to enter the following initial data. Add the following to the `load()` method of `/src/DataFixtures/AppFixtures.php`

```php
    $p1 = new Product();
    $p1->setDescription('bag of nails');
    $p1->setPrice(5.00);
    $p1->setCategory('hardware');
    $manager->persist($p1);
    
    $p2 = new Product();
    $p2->setDescription('sledge hammer');
    $p2->setPrice(10.00);
    $p2->setCategory('tools');
    $manager->persist($p2);
    
    $p3 = new Product();
    $p3->setDescription('small bag of washers');
    $p3->setPrice(3.00);
    $p3->setCategory('hardware');
    $manager->persist($p3);

    $manager->flush();
```


Now migrate your updated Entity structure to the database and load those fixtures. Figure \ref{query1} shows the list of products you should visiting the `/product` route. (NOTE: Unless you've added Boostrap, your page may not look so neat...)

![Animated hamburger links for narrow browser window. \label{query1}](./03_figures/part03/q1_productsList.png)

## Add new route and controller method for category search

Add a new method to the `ProductController` that has the URL route pattern `/product/category/{category}`. We'll name this method `categorySearch(...)` and it will allow us to refine the list of products to only those with the given `Category` string:

```php
    #[Route('/category/{category}', name: 'app_product_search', methods: ['GET'])]
    public function search(string $category, ProductRepository $productRepository): Response
    {
        $products = $productRepository->findByCategory($category);

        $template = 'product/index.html.twig';
        $args = [
            'products' => $products,
            'category' => $category
        ];
        return $this->render($template, $args);
    }
```

First, we are getting a string from the URL that follows `/product/category/`. **All** routes defined in the CRUD generated `ProductController` are prefixed with `/product`, due to the annoation comment that is declared **before** the class declaration:

```php
    #[Route('/product')]
    class ProductController extends AbstractController
    {
        ... controller methods here ...
    }
```

Whatever appears **after**  `/product/category/` in the URL will be put into variabled `$category` by the Symfony routing system, because of the `Route` attribute:

```php
   #[Route('/category/{category}', name: 'app_product_search', methods: ['GET'])]
```

We get a reference to an object that is an instance of the `ProductRepository` from the method parameter `productRepository` - this object will be created automatically for us by the Symfony param-converter:

```php
    public function search(string $category, ProductRepository $productRepository)
```

We could get an array `products` of **all** `Product` objects from the database by writing:

```php
    $products =  $productRepository->findByCategory($category);
```

But Doctrine repository classes also give us free **helper** methods, that provide `findBy` and `findOneBy` methods for the properties of an Entity class. Since Entity class `Product` has a property `name`, then we get for free the Doctrine query method `findByName(...)` to which we can pass a value of `name` to search for. So we can get the array of `Product` objects whose `name` property matches the paramegter `category` as follows:

```php
    $products =  $productRepository->findByCategory($category);
``` 

Finally, we'll pass both the `$products` array, and the text string `$category` as variables to the `index` list Products Twig template:

```php
    $template = 'product/index.html.twig';
    $args = [
        'products' => $products,
        'category' => $category
    ];

    return $this->render($template, $args);
```


## Testing our search by category

If we now visit `/products/category/tools` we should see a list of only those Products with category = `tools`. See Figure \ref{query3} for a screenshot of this.

Likewise, for  `/products/category/hardware` - see  Figure \ref{query4}.

![Only `tools` Products. \label{query3}](./03_figures/part03/q3_tools.png){width=80%}

![Only `hardware` Products. \label{query4}](./03_figures/part03/q4_hardware.png){width=80%}

![Only `abc` Products (i.e none!). \label{query5}](./03_figures/part03/q5_abc.png){width=80%}

If we try to search with a value that does not appear as the `category` String property for any Products, no products will be listed.  See Figure \ref{query5}.

