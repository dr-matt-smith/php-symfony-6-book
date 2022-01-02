# Custom database queries

## Search for exact property value (project `query01`)

Let's create a simple database schema for hardware products, and then write some forms to query this database.

Use `make:entity` to create a new Entity class Product, with the following properties:

- description: String
- price: Float
- category: String

Use `make:crud` to generate the CRUD pages for Product entities.

## Fixtures

NOTE: You may need to add the ORM Fixtures library to this project:

```bash
composer req orm-fixtures --dev
```

Use `make:fixtures ProductFixtures` to create a fixtures class, and write fixtures to enter the following initial data:

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
```


Now migrate your updated Entity structure to the database and load those fixtures. Figure \ref{query1} shows the list of products you should visiting the `/product` route.

![Animated hamburger links for narrow browser window. \label{query1}](./03_figures/part03/q1_productsList.png)

## Add new route and controller method for category search

Add a new method to the `ProductController` that has the URL route pattern `/product/category/{category}`. We'll name this method `categorySearch(...)` and it will allow us to refine the list of products to only those with the given `Category` string:

```php
    /**
     * @Route("/category/{category}", name="product_search", methods={"GET"})
     */
    public function search($category): Response
    {

        $productRepository = $this->getDoctrine()->getRepository('App:Product');
        $products =  $productRepository->findByCategory($category);

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
    /**
     * @Route("/product")
     */
    class ProductController extends AbstractController
    {
        ... controller methods here ...
    }
```

Whatever appears **after**  `/product/category/` in the URL will be put into variabled `$category` by the Symfony routing system, because of the `Route` anntation comment:

```php
    /**
     * @Route("/category/{category}", name="product_search", methods={"GET"})
     */
```

We get a reference to an object that is an instance of the `ProductRepository` from this line:

```php
    $productRepository = $this->getDoctrine()->getRepository('App:Product');
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

## Aside: How to the free 'helper' Doctrine methods work?

PHP offers a runtime code reflection (or interpreter pre-processing if you prefer), that can intercept calls to non-existent methods of a class. We use the special **magic** PHP method `__call(...)` which expects 2 parameters, one for the non-existent method name, and one as an array of argument values passed to the non-existent method:

```php
    public function __call($methodName, $arguments)
    {
        ... do something with $methodName and $arguments
    }
```

Here is a simple class (put it in `/src/Util/ExampleRepository.php` in you want to try this) that demonsrtates how Doctrine uses `__call' to identify which Entity property we are trying to query by:

```php
    <?php
    namespace App\Util;
    
    /*
     * class to demonstrate how __call can be used by Doctrine repositories ...
     */
    class ExampleRepository
    {
        public function findAll()
        {
            return 'you called method findAll()';
        }
    
        public function __call($methodName, $arguments)
        {
            $html = '';
            $argsString = implode(', ', $arguments) . "\n";
    
            $html .= "you called method $methodName\n";
            $html .= "with arguments: $argsString\n";
    
            $result = $this->startsWithFindBy($methodName);
            if($result){
                $html .= "since the method called started with 'findBy'"
                . "\n it looks like you were searching by property '$result'\n";
            }
    
            return $html;
        }
    
        private function startsWithFindBy($name)
        {
            $needle = 'findBy';
            $pos = strpos($name, $needle);
    
            // since 0 would evaluate to FALSE, must use !== not simply !=
            if (($pos !== false) && ($pos == 0)){
                return substr($name, strlen($needle)); // text AFTER findBy
            }
    
            return false;
        }
    }
```

You could add a new method to the `DefaultController` class to see this in action as follows:

```php
    /**
     * @Route("/call", name="call")
     */
    public function call()
    {
        // illustrate how __call workds
        $exampleRepository = new ExampleRepository();

        $html = "<pre>";
        $html .=  "----- calling findAll() -----\n";
        $html .= $exampleRepository->findAll();

        $html .=  "\n\n----- calling findAllByProperty() -----\n";
        $html .= $exampleRepository->findByName('matt', 'smith');

        $html .=  "\n----- calling badMethodName() -----\n";
        $html .= $exampleRepository->badMethodName('matt', 'smith');

        return new Response($html);
    }
```


See Figure \ref{query2} shows the `ExampleRepository` output you should visiting the `/call` route. We can see that:

- a call to `findAll()` works fine, since that is a defined public method of the class

- a call to `findByName(...)` would work fine, since we can use `__call(...)` to identify that this was a call to a helper `findBy<property>(...)` method

    - and we could add logic to check that this is a property of the Entity class and build an appropriate query from the arguments
    
- a call to `badMethodName(...)` is caught by `__call(...)`, but fails our test for starting with `findBy`, and so we can ignore it 

    - or log error or throw Exception or whatever our program spec says to do in these cases...

![Output from our ExampleRepository `__call` demo. \label{query2}](./03_figures/part03/q2_callExample.png)

## Testing our search by category

If we now visit `/products/category/tools` we should see a list of only those Products with category = `tools`. See Figure \ref{query3} for a screenshot of this.

Likewise, for  `/products/category/hardware` - see  Figure \ref{query4}.

![Only `tools` Products. \label{query3}](./03_figures/part03/q3_tools.png){width=80%}

![Only `hardware` Products. \label{query4}](./03_figures/part03/q4_hardware.png){width=80%}

![Only `abc` Products (i.e none!). \label{query5}](./03_figures/part03/q5_abc.png){width=80%}

If we try to search with a value that does not appear as the `category` String property for any Products, no products will be listed.  See Figure \ref{query5}.


