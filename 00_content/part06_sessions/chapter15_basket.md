
# Working with a session 'basket' of products


## Shopping cart of products (project `session04`)

When you're leaning sessions, you need to build a 'shopping cart'! Let's create CRUD for some Products and then offer a shopping baset.

We will have an `basket` item in the session, containing an array of `Product` objects adding the the basket. This array will be indexed by the `id` property of each Product (so we won't add the same Product twice to the array), and items are easy to remove by unsetting.

## Create a new project with the required packages

Let's start with a brand new project to work with for shopping baskets in sessions:

```bash
    $ symfony new --webapp session04
```

Now let's add the packages for working with databases:

```bash
    $ composer req orm-fixtures
```


## Create a Product entity & generate its CRUD

Make a new `Product` entity:

```bash
    $ symfony console make:entity Product
```

In in the interactive mode add the following properties:

- `description` (defaults: string/255/not nullable)
- `image` (defaults: string/255/not nullable)
- `price` (float)

You should now have an entity class `src/Entity/Product` with accessor methods and database annotation comments for each property. You should also have a repository class `src/Repository/ProductRepository`.

Configure your `.env` database settings, e.g. to setup for MySQL database `sessions01` have the following:

```
    DB_USER=root
    DB_PASSWORD=pass
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_NAME=sessions01
    DATABASE_URL=mysql://${DB_USER}:${DB_PASSWORD}@${DB_HOST}:${DB_PORT}/${DB_NAME}
```

Generate the database, and migrations and migrate:

```
    $ symfony console doctrine:database:create
    $ symfony console make:migration
    $ symfony console doctrine:migrations:migrate
```

Then generate CRUD for this entity (i.e. a ProductController, templates in `/templates/product/`, and a form class `src/Form/ProductType.php`):

```bash
    $ symfony console make:crud Product
```

## Homepage - link to products home

Create a default controller:

```bash
    $ symfony console make:controller Default
```

Set this controller's route to the website root `/` (rather than `/default`), and make the Twig template for the default homepage be a link to generated route `app_product_index`:

```twig
    <p>
        Hello World
    </p>

    <a href="{{ url('app_product_index') }}">list of products</a>
```

## Write some `Product` fixtures

Edit class `/src/DataFixturs/AppFixtures.php` to add 3 initial products into the DB:

```php
    public function load(ObjectManager $manager): void
    {
        $p1 = new Product();
        $p1->setDescription('hammer');
        $p1->setImage('hammer.png');
        $p1->setPrice(5.99);
        $manager->persist($p1);

        $p2 = new Product();
        $p2->setDescription('ladder');
        $p2->setImage('ladder.png');
        $p2->setPrice(19.99);
        $manager->persist($p2);

        $p3 = new Product();
        $p3->setDescription('bucket of nails');
        $p3->setImage('nails.png');
        $p3->setPrice(0.99);
        $manager->persist($p3);

        $manager->flush();
    }
```

Run the server and view the products into the database, e.g.:

```
    Id	Description	     Image	        Price
    1	hammer	         hammer.png	    5.99
    2	ladder	         ladder.png	    19.99
    3	bucket of nails	 nails.png       0.99
```

## Basket index: list basket contents

We'll write our code in a new controller class `BasketController.php` in directory `/src/Controller/`.

Generate our new controller:

```bash
    $ symfony console make:controller Basket
```

Here is our class:

```php
    namespace App\Controller;

    use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Component\Routing\Annotation\Route;

    #[Route('/basket', name: 'basket_')]
    class BasketController extends AbstractController
    {
        #[Route('/', name: 'index')]
        public function index(): Response
        {
            $template = 'basket/index.html.twig';
            $args = [];
            return $this->render($template, $args);
        }
    }
```



Note:

- we have added the `#[Route(...)]` prefix `/basket` to all controller actions in this class by writing a  `Route` attribute for the class declaration.

- the basket index controller action is very simple, since all the work extracting values from the session will be done by our Twig template. So our index action simply returns the Twig rendering of template `basket/index.html.twig`

## Controller class - adding `RequestStack` property

As before, we access sessions through the **RequestStack**. So the controller needs to declare a private `RequestStack` variable, which we initialised with the Symfony param-converter via a constructor method:

```php
    class DefaultController extends AbstractController
    {
        private RequestStack $requestStack;

        public function __construct(RequestStack $requestStack)
        {
            $this->requestStack = $requestStack;
        }
```

Note, ensure you add the following `use` statement at the top of the class using this code:

```php
    use Symfony\Component\HttpFoundation\RequestStack;
```

## Controller method - `clear()`

Let's write another simple method next - a method to remove any `basket` attribute from the session. We can achieve this with the statement `$session->remove('basket')`:

```php
    #[Route('/empty', name: 'empty')]
    public function empty(): Response
    {
        $session = $this->requestStack->getSession();
        $session->remove('basket')

        return $this->redirectToRoute('app_default');
    }
```


Let's see how each route is prefixed with `/basket` and each route name is prefixed with `basket_` by listing routes at the CLI:

```bash
    $ symfony console debug:router
    -------------------------- ---------- -------- ------ -----------------------------------
    Name                       Method     Scheme   Host   Path
    -------------------------- ---------- -------- ------ -----------------------------------
    basket_index               ANY        ANY      ANY    /basket/
    basket_clear               ANY        ANY      ANY    /basket/clear
    app_default                ANY        ANY      ANY    /
    app_product_index          GET        ANY      ANY    /product/
    app_product_new            GET|POST   ANY      ANY    /product/new
    app_product_show           GET        ANY      ANY    /product/{id}
    app_product_edit           GET|POST   ANY      ANY    /product/{id}/edit
    app_product_delete         POST       ANY      ANY    /product/{id}
```


## Debugging sessions in Twig

As well as the Symfony profiler, there is also the powerful Twig function `dump()`. This can be used to interrogate values in the session.

You can either dump **every** variable that Twig can see, with `dump()`. This will list arguments passed to Twig by the controller, plus the `app` variable, containing session data and other applicatiton object properties.

Or you can be more specific, and dump just a particular object or variable. For example we'll be building an attribute stack session array named `basket`, and the contents of this array can be dumped in Twig with the following statement:

```twig
    {{ dump(app.session.get('basket')) }}
```

You might put this at the bottom of the HTML <body> element in your `base.html.twig` main template while debugging this shopping basket application:

```
    <!DOCTYPE html>
    ...
        <body>
            {% block body %}{% endblock %}

            <hr>
            contents of session 'basket'

            {{ dump(app.session.get('basket')) }}

        </body>
```


Figure \ref{basket_dump} shows  an example of what we'll see on the home page from this Twig `dump()` statement if there is one item (Product 1) in the session basket.

![Screenshot of home page dumping session `basket` variable contents. \label{basket_dump}](./03_figures/part05_sessions/10_basket_dump.png)





## Adding an object to the basket

The logic to add an object into our session `basket` array requires a little work.

We'll make things easy for ourselves - using the Symfony Param-Converter. So a product `id` is in the URL `/add/<id>`, but for our method declaration we say we are expecting a reference to a `Product` record `$product`. Symfony will go off and retrieve the row from the database corresponding to the `id`, and return us a reference to a `Product` object containing the properties from the database.


Since we'll be working with `Product` objects, we need to add a `use` statement at the top of our `BasketController` class:

```php
    use App\Entity\Product;
```

We need to get a PHP array `$products`, that is either what is currently in the session, or a new empty array if no such array was found in the session.DOCTYPE

Let's add a new method `addToBasket()` to our `BasketController` class:


```php
    #[Route('/add/{id}', name: 'add')]
    public function addToBasket(Product $product)
    {
        // default - new empty array
        $products = [];

        // if 'products' array in session, retrieve and store in $products
        $session = $this->requestStack->getSession();
        if ($session->has('basket')) {
            $products = $session->get('basket');
        }

        ...
```

Note above, that we are relying on the 'magic' of the Symfony param-converter here, so that the integer 'id' received in the request is converted into its corresponding Elective object for us.

Next we get the 'id' of the Product object, and see whether it can be found already in array `propducts`. If if is not already in the array, then we add it to the array (with the 'id' as key), and store the updated array in the session under the attribute bag key `basket`:

```php
        ...
        // get ID of product
        $id = $product->getId();

        // only try to add to array if not already in the array
        if (!array_key_exists($id, $products)) {
            // append $product to our list
            $products[$id] = $product;

            // store updated array back into the session
            $session->set('basket', $products);
        }
        ...
```

Finally (whether we changed the session `basket` or not), we redirect to the basket index route:

```php
        ...
        return $this->redirectToRoute('basket_index');
    }
```

## The delete action method

The delete action method is very similar to the add action method. In this case we never need the whole `Product` object, so we can keep the integer `id` as the parameter for the method.

We start (as for add) by ensuring we have a PHP variable array `$products`, whether or not one was found in the session.

```php
    #[Route('/delete/{id}', name: 'delete')]
    public function delete(int $id): Response
    {
        // default - new empty array
        $products = [];

        // if 'products' array in session, retrieve and store in $products
        $session = $this->requestStack->getSession();

        if ($session->has('basket')) {
            $products = $session->get('basket');
        }
        ...
```

Next we see whether an item in this array can be found with the key `$id`. If it can, we remove it with `unset` and store the updated array in the session attribute bag with key `basket`.

```php
        ...
        // only try to remove if it's in the array
        if (array_key_exists($id, $products)) {
            // remove entry with $id
            unset($products[$id]);

            if (sizeof($products) < 1) {
                return $this->redirectToRoute('basket_clear');
            }


            // store updated array back into the session
            $session->set('basket', $products);
        }
        ...
```

Note - if there are no items left in the baset, we redirect to the clear action to remove the basket attribute completely from the session.

Finally (whether we changed the session `basket` or not), we redirect to the basket index route:

```php
        ...
        return $this->redirectToRoute('basket_index');
    }
```

## The Twig template for the basket index action

The work extracting the array of products in the basket and displaying them is the task of template `index.html.twig` in `/templates/basket/`.

First, after nice big `<h1>` heading, we attempt to retrieve item `basket` from the session:

```html
    <h1>Basket contents</h1>

    {% set basket = app.session.get('basket') %}
```

Next we have a Twig `if` statement, displaying an empty basket message if `basket` is null, i.e.:

```html
    {% if basket is null %}
        <p>
            you have no products in your basket
        </p>
```


The we have an `else` statement (for when we did retrieve an array), that loops through creating an unordered HTML list of the basket items:
```
    {% else %}
        <ul>
            {% for product in basket %}
                <li>
                    <hr>
                    {{ product.id }} :: {{ product.description }}
                    <a href="{{ path('basket_delete', { 'id': product.id }) }}">(remove)</a>
                </li>
            {% endfor %}
        </ul>
    {% endif %}
```

Note that a link to the `delete` action is offered at the end of each list item as text `(remove`).

Finally, a paragraph is offered, containing a list to clear all items from the basket:

```html
    <p>
        <a href="{{ path('basket_clear') }}">CLEAR all items in basket</a>
    </p>
```



Figure \ref{electives_basket} shows a screenshot of the basket index page - listing the basket contents.

![Shopping basket of elective modules. \label{electives_basket}](./03_figures/part05_sessions/4_baset_page.png)


## Adding useful links to our `base.html.twig` template

Let's add those useful navigation links to the top of every page. Add the following just before the `body` block is defined in template `base.html.twig`:

```twig
    <nav>
        <ul>
            <li><a href="{{  url('app_default') }}">home</a></li>
            <li><a href="{{  url('app_product_index') }}">list of products</a></li>
            <li><a href="{{  url('basket_index') }}">basket</a></li>
        </ul>
    </nav>
```

Every page on the website should now show these links.,

## Adding the 'add to basket' link in the list of products

To link everything together, we can now add a link to 'add to basket' in our products CRUD index template. So when we see a list of products we can add one to the basket, and then be redirected to see the updated basket of products. We see below an extra list item for path `basket_add` in template `index.html.twig` in directory `/templates/product/`.

We add this line:

```twig
   <a href="{{ path('basket_add', { 'id': product.id }) }}">add to basket</a>
```

to the end of the table cell displaying each `Product`

```twig
    {% for product in products %}
        <tr>
            <td>{{ product.id }}</td>
            <td>{{ product.description }}</td>
            <td>{{ product.image }}</td>
            <td>{{ product.price }}</td>
            <td>
                <a href="{{ path('product_show', {'id': product.id}) }}">show</a>
                <a href="{{ path('product_edit', {'id': product.id}) }}">edit</a>

                <!-- ADD TO BASKET -->
                <a href="{{ path('basket_add', { 'id': product.id }) }}">add to basket</a>
            </td>
        </tr>
    {% else %}
        <tr>
            <td colspan="5">no records found</td>
        </tr>
    {% endfor %}
```

Figure \ref{add_to_basket} shows a screenshot of the list of products page, each with an 'add to basket' link.

![List of Products with 'add to basket' link. \label{add_to_basket}](./03_figures/part05_sessions/5_products_list.png)
