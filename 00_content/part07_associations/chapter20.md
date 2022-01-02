
# Many-to-one (e.g. Products for a single Category)


## Basic list products for current Category (project `associations02`)

First we'll do the minimum to add a list of all the Projects associated with a single Category, then later we'll do it in a nicer way ...


## Add `getProducts()` for Entity Category

We need to add a getter for products in `/src/Entity/Category.php` that returns a Doctrine `Collection` of objects:

```php
    public function getProducts():Collection
    {
        return $this->products;
    }
```
We need to add an appropite `use` statement for 

```php
    use Doctrine\Common\Collections\Collection;
```

## Add a `__toString()` for Entity Products

We need to add a 'magic method' `__toString()` to Entity Product, since our form builder will need a string for each Product in its list to display:

Add  `__toString()`to `/src/Entity/Product.php`. We'll just list `id` and `description`:

    public function __toString()
    {
        return $this->id . ': ' . $this->getDescription();
    }

## Make Category form type add `products` property

Earlier we added the special `products` property to entity Category, which is the 'many' link to all the Products for the current Category object. We will now add this property to our Category form class `CategoryType`, so that the form created will display all Products found by automatically following that relationship^[I.e. Doctrine will magically run something like 'SELECT * FROM product WHERE product.category = category.id' for the current Category object.].

In `/src/Form/CategoryType.php` add `add('products')` to our `buildForm(```)` method:

```php
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('products')
        ;
    }
```

If we visit the 'edit' page for a Category now, we can see a read-only multiple value list box displayed for `products`, with all Products for the current Category selected.

While it doesn't look very nice, our inverse-relationship is all working fine.

 See Figure \ref{category_products}.

![Screenshot of products list for Category edit. \label{category_products}](./03_figures/part07_associations/3_edit_category.png)

## Adding a nicer list of Products for Category show page

Let's add a **nice** list of Products for a Category on the Category show page.

In the Twig `/src/templates/show.html.twig` we have a reference to the `category` object. We can get an array of associated `Product` objects by writing simply `category.products`, so we can loop through this:

```twig
    {% for product in category.products %}
        {{ product.id }} :: {{ product.description }}
        <br>
    {% else %}
        (no products for this category)
    {% endfor %}
```

This will output some HTML like this:

```html
    1 :: hammer
    <br>
    3 :: bag of nails
    <br>
```

So we can simply add a new HTML table row in our `show.html.twig` template, listing Products as follows:

```twig
        <tr>
            <th>Id</th>
            <td>{{ category.id }}</td>
        </tr>
        <tr>
            <th>Name</th>
            <td>{{ category.name }}</td>
        </tr>

        <tr>
            <th>Products for this Category</th>
            <td>
                {% for product in category.products %}
                    {{ product.id }} :: {{ product.description }}
                    <br>
                {% else %}
                    (no products for this category)
                {% endfor %}
            </td>
        </tr>
```

 See Figure \ref{category_show_products}.

![Screenshot of improved Category show page. \label{category_show_products}](./03_figures/part07_associations/4_show_category_products.png)

## Improving the Edit form (project `associations03`)

That multi-selection form element was not very nice for our Edit/New forms.

Let's refactor template `/templates/category/_form.html.twig` to display the list of products for a Category in a nicer way. This Twig 'partial' is use both for the **new** Category form and for the **edit** category form.

Our Twig form did contain:

```twig
    {{ form_start(form) }}
        {{ form_widget(form) }}

        <button>{{ button_label|default('Save') }}</button>
    {{ form_end(form) }}
```

Since we want to customise how form elements are displayed, we need to replace `{{ form_widget(form) }}` with our own form elements and HTML.

As explained in an earlier chapter on customising Symfony generated forms, there are 3 parts to a Symfomny form output by `{{ form_widget(form) }}`:

```twig
    {{ form_start(form) }}
    {{ form_widget(form) }}
    {{ form_end(form) }}
```

We don't want the default form elements, then we can display them separately with `form_row`, e.g. we have 2 properties for Entity class `Category`, the name and the collection of related products:

```twig
    {{ form_row(form.name) }}
    {{ form_row(form.products) }}
```

We wish to keep the default rendering of the `name` property, so the start of our customised form will be:

```twig
    {{ form_start(form) }}
    
    {{ form_row(form.name) }}
```

Now we have to decide how to render the `products` array. Let's do something very similar to our show form, and loop through creating list items for each:


```twig
    <div>
        Products for this Category:
        <ul>
            {% for product in form.vars.value.products %}
                <li>
                    <a href="{{ url('product_show', {'id':product.id}) }}">
                        {{ product.id }} :: {{ product.description }}
                    </a>
                </li>
            {% else %}
                <li>
                    (no products for this category)
                </li>
            {% endfor %}
        </ul>
    </div>
```

As you can see, we can access the array `products` of our Category object with expression:

```
    form.vars.value.products
```

So we can write a `for`-loop around this array.

Note - we still need to render the `products` form widget, otherwise Symfony will end the form HTML with all properties not yet rendered. So we can **hide** the default rendering for a selection element by wrapping an HTML comment around the default HTML `select` form element. We also need to display the button `Save` button, since we are rendering the form in pieces:

```    
        <button class="btn">{{ button_label|default('Save') }}</button>
    
    <!--
        {{ form_widget(form.products) }}
    -->
```

We end with `{{ form_end(form) }}`, so the full listing for our new/edit form template `/templates/category/_form.html.twig` is:

```twig
    {{ form_start(form) }}
    
    {{ form_row(form.name) }}
    
    <div>
        Products for this Category:
        <ul>
            {% for product in form.vars.value.products %}
                <li>
                    <a href="{{ url('product_show', {'id':product.id}) }}">
                        {{ product.id }} :: {{ product.description }}
                    </a>
                </li>
            {% else %}
                <li>
                    (no products for this category)
                </li>
            {% endfor %}
        </ul>
    </div>
    
        <button class="btn">{{ button_label|default('Save') }}</button>
    
    <!--
        {{ form_widget(form.products) }}
    -->
    
    {{ form_end(form) }}
```


Figure \ref{category_edit_customised} shows a screenshot of our customised Edit form.

![Screenshot of customised edit form. \label{category_edit_customised}](./03_figures/part07_associations/5_edit_form_customised.png)

## Creating related objects as Fixtures (project `associations04`)

A good way to get a feel for how the Doctrine ORM relates objects, **not** object IDs, is through fixtures. So we can create a `Category` object, and also create a `Product` object, whose `category` property is a reference to the `Category` object. E.g. here are 3 categories and one obnject (hammer) linked to the small items category:

```php
    namespace App\DataFixtures;
    
    use Doctrine\Bundle\FixturesBundle\Fixture;
    use Doctrine\Common\Persistence\ObjectManager;
    use App\Entity\Category;
    use App\Entity\Product;
    
    class CategoryFixtures extends Fixture
    {
        public function load(ObjectManager $manager)
        {
            // ------ categories ------
            $catDefault = new Category();
            $catDefault->setName('(default)');
    
            $catSmall = new Category();
            $catSmall->setName('small items');
    
            $catLarge = new Category();
            $catLarge->setName('large items');
    
            $manager->persist($catDefault);
            $manager->persist($catSmall);
            $manager->persist($catLarge);
            
            // ------- product ----------
            $p1 = new Product();
            $p1->setDescription('hammer');
            $p1->setPrice(9.99);
            $p1->setImage('hammer.png');
            $p1->setCategory($catSmall);
    
            $manager->persist($p1);
    
            $manager->flush();
        }
```


## Using Joins in custom Repository classes

Where Doctrine really shows its worth is when we want to work with tables joined by related properties.

Below a custom Repository method has been created that lists all houses, whose related status object has the title 'for sale':

```php
    class HouseRepository extends ServiceEntityRepository
    {
        public function __construct(RegistryInterface $registry)
        {
            parent::__construct($registry, House::class);
        }

        public function findAllForSale()
        {
            return $this->createQueryBuilder('house')
                ->leftJoin('house.status', 'status')

                ->andWhere('status.title = :title')
                ->setParameter('title', 'for sale')
                ->getQuery()
            ->execute();

        }

    }
```

By putting complex queries into custom methods in the Repository class, the code in our controllers stays very simple, e.g. below we see an array of all houses 'for sale' is being passed to the default home page controller method, to list on the website home page:

```php
    class DefaultController extends Controller
    {
        /**
         * @Route("/", name="home_page")
         */
        public function index(HouseRepository $houseRepository)
        {
            $houses = $houseRepository->findAllForSale();

            $template = 'default/index.html.twig';
            $args = [
            'houses' => $houses
            ];
            return $this->render($template, $args);
        }

    }
```

Once again, we see the power of the Symfony paramconvertor, in that to get a reference to a HouseRepository object, we just add a method parameter `HouseRepository $houseRepository`, and as if by magic, we can just start using the repository object!

