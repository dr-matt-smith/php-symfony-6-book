
# Database relationships (Doctrine associations)

## Information about Symfony and databases

Learn about Doctrine relationships and associates at the Symfony documentation pages:

- [https://symfony.com/doc/current/doctrine.html#relationships-and-associations](https://symfony.com/doc/current/doctrine.html#relationships-and-associations)

- [https://symfony.com/doc/current/doctrine/associations.html](https://symfony.com/doc/current/doctrine/associations.html)


## Create a new project from scratch (project `associations01`)

Create a new project `associations01`:

```bash
    $ symfony new --webapp associations01
```

Now `cd` into the project in a terminal, and add the fixtures package:

```bash
     composer req orm-fixtures
```

Test the server, and if necessary, remove the `encore_entry_` lines inside the `stylesheets` and `javascripts` blocks in the base template (`/templates/base.html.twig`).



## Categories for Products

Let's work with a project where we have `Products`, and two categories of Product:

- large items

- small items


We are aiming for a Many-(products)-to-One-(category). So we can make things easier for ourselves by creating the 'One' entity first - in this case `Category`:

### Entity Category
Generate a Entity `Category`:

```bash
    $ symfony console make:entity Category
```

Add to the `Category` property:

- name (String, 255 - take all the defaults)

### Category `__toString()`

Since we'll be selecting a category for each product in the CRUD pages, we need to add a toString method to the `Category` entity class, so that HTML dropdown list can be generated.add

So add this method to `/src/Entity/Category`:

```php
    public function __toString(): string
    {
        return $this->name;
    }
```


### Entity Product

Generate a Entity `Product`:

```bash
    $ symfony console make:entity Product
```

Now generate a `Product` entity, with properties:

- `description` (text)

- `image` (text)

- `price` (float)



## Defining the many-to-one relationship from Product to Category

We now edit our `Product` entity, declaring a property `category` that has a Many-to-One relationship with entity `Category`. I.e., Many products relate to One category.

NOTE: We need to allow `null` for a Product's category:

- when it is first created (to generate a form the easy way)

- to allow a category to be removed from a Product


Also, accept the **free** reciprocal `products` array property offered to be added to the `Category` entity.

So your terminal interaction should look something like the following:

```bash
    $ symfony console make:entity Product

     Your entity already exists! So let's add some new fields!
     New property name (press <return> to stop adding fields):
     > category

     Field type (enter ? to see all types) [string]:
     > relation

     What class should this entity be related to?:
     > Category

    What type of relationship is this?
     ------------ -----------------------------------------------------------------------
      Type         Description
     ------------ -----------------------------------------------------------------------
      ManyToOne    Each Product relates to (has) one Category.
                   Each Category can relate to (can have) many Product objects
      .. etc.

     Relation type? [ManyToOne, OneToMany, ManyToMany, OneToOne]:
     > ManyToOne

     Is the Product.category property allowed to be null (nullable)? (yes/no) [yes]:
     >

     Do you want to add a new property to Category so that you can access/update Product objects from it - e.g. $category->getProducts()? (yes/no) [yes]:
     >

     A new property will also be added to the Category class so that you can access the related Product objects from it.

     New field name inside Category [products]:
     >

     updated: src/Entity/Product.php
     updated: src/Entity/Category.php
```

## Create a new database and migration

Now we have finished making our related entities, we can generate the corresponding database schema.

Edit your `.env` for a new MySQL database, and make a migration, and run the migration to create your DB schema for this entity.

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



## Add small and large item Category

Let's create two categories:

- `small items`

- `large items`

You could run the server and manually created these at CRUD page `/category/new`. Alternatively, you could create a `CategoryFixtures` class to automatically add these to the database:

```php
    namespace App\DataFixtures;

    use Doctrine\Bundle\FixturesBundle\Fixture;
    use Doctrine\Persistence\ObjectManager;
    use App\Entity\Category;

    class AppFixtures extends Fixture
    {
        public function load(ObjectManager $manager): void
        {
            $cat1 = new Category();
            $cat1->setName('small items');
            $manager->persist($cat1);

            $cat2 = new Category();
            $cat2->setName('large items');
            $manager->persist($cat2);

            $manager->flush();
        }
    }
```

## Making the CRUD for the 2 entities


Let's now use the make tool to generate the CRUD pages for both entities:


```bash
    $ symfony console make:crud Product

    $ symfony console make:crud Category
```

In both cases, accept the default name offered for the new Controller class.


## Drop-down menu of categories when creating/editing `Product`s

Now we are automatically given a drop-down list of `Category` items to choose from when we visit `/product/new` to create a new `Product` object. See Figure \ref{category_dropdown}.

![Screenshot of `Category` dropdown for new `Product` form. \label{category_dropdown}](./03_figures/part07_associations/1_new_product.png)

So you can now create some `Product` items, each linked to a `Category` (or leaving the `Category` as null).

## Adding display of Category to list and show Product

Remember, with the Doctrine ORM (Object-Relational Mapper), if we have a reference to a `Product` object, in PHP we can get its `Category` as follows:

```php
    $category = $product->getCategory();

    if(null != $category)
        // do something with $category
```

In Twig its even simpler, since the dot-syntax finds the public `getter` automatically:

```twig
    Category = {{ product.category }}
```

So we can update the Product list Twig template to show Category.

Update template `/templates/product/index.html.twig` as follows:

```twig
    {% for product in products %}
        <tr>
            <td>{{ product.id }}</td>
            <td>{{ product.description }}</td>
            <td>{{ product.image }}</td>
            <td>{{ product.price }}</td>
            <td>{{ product.category.name }}</td>
            ...
```

and add a new column header:

```twig
    <tr>
        <th>Id</th>
        <th>Description</th>
        <th>Image</th>
        <th>Price</th>
        <th>Category</th>
        <th>actions</th>
```

And we can update the Product show Twig template to show Category as follows (`/templates/product/show.html.twig`):

```twig
    <tr>
        <th>Id</th>
        <td>{{ product.id }}</td>
    </tr>
    <tr>
        <th>Description</th>
        <td>{{ product.description }}</td>
    </tr>
    <tr>
        <th>Image</th>
        <td>{{ product.image }}</td>
    </tr>
    <tr>
        <th>Price</th>
        <td>{{ product.price }}</td>
    </tr>

    <tr>
        <th>Category</th>
        <td>{{ product.category.name }}</td>
    </tr>
```

See Figure \ref{category_dropdown} to see Category  for each Product in the list.

![Screenshot of list of Products with their Category names. \label{product_list_category}](./03_figures/part07_associations/2_list_product_categories.png)

If you want the nice table formatting add the following into the `<head>` element in the base Twig template:

```html
    <style>
        table, td, tr, th {
            padding: 0.5rem;
            margin: 0.5rem;
        }

        td {
            border: solid black 0.1rem;
        }
    </style>
```

## Showing category in the SHOW page

We can also display the `Category` name in our `Product` show Twig template.

Update file `/templates/product/show.html.twig` as follows:

```twig
    ...
    <h1>Product</h1>

    <table class="table">
        <tbody>
            <tr>
                <th>Id</th>
                <td>{{ product.id }}</td>
            </tr>
            <tr>
                <th>Description</th>
                <td>{{ product.description }}</td>
            </tr>
            <tr>
                <th>Image</th>
                <td>{{ product.image }}</td>
            </tr>
            <tr>
                <th>Price</th>
                <td>{{ product.price }}</td>
            </tr>
            <tr>
                <th>Category</th>
                <td>{{ product.category }}</td>
            </tr>
        </tbody>
    </table>
```

Twig follows getter methods by just the the property name. So `{{ product.category.name }}` is equivalent to PHP `$product->getCategory()->getName()`!

In fact, since we added a toString() method to entity class `Category`, we can simply write `{ product.category }` which will invoke the toSring() method, which returns the object's `name` string property.


## Foreign key columns - associations in the database

While the Doctrine ORM allows us to write code working with object references, in the actual database associations between objects are stored through the use of columns of foreign key IDs.

Figures \ref{new_products} shows a screenshot of products with their category id.

![Screenshot of MySQLWorkbench listing products with `category_id`. \label{new_products}](./03_figures/lab05_relationships/1_product_list.png)



## Further reading about associations

Learn more in the Symfony documentation:

- [https://symfony.com/doc/current/doctrine/associations.html](https://symfony.com/doc/current/doctrine/associations.html)

