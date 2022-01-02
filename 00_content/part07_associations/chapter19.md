
# Database relationships (Doctrine associations)

## Information about Symfony 4 and databases

Learn about Doctrine relationships and associates at the Symfony documentation pages:

- [https://symfony.com/doc/current/doctrine.html#relationships-and-associations](https://symfony.com/doc/current/doctrine.html#relationships-and-associations)

- [https://symfony.com/doc/current/doctrine/associations.html](https://symfony.com/doc/current/doctrine/associations.html)


## Create a new project from scratch (project `associations01`)

Create a new project, adding the usual packages for database and CRUD generation:

- server
- make
- twig
- annotations
- doctrine
- form
- validation
- annotations
- security-csrf
- orm-fixtures

## Categories for Products

Let's work with a project where we have `Products`, and two categories of Product:

- large items

- small items

So we need to generate a Entity `Category` , with a `name` property:


```bash
     $ php bin/console make:entity Category
     
      created: src/Entity/Category.php
      created: src/Repository/CategoryRepository.php
      
      Entity generated! Now let's add some fields!
      You can always add more fields later manually or by re-running this command.
     
      New property name (press <return> to stop adding fields):
      > name 

      ... (hit <RETURN> for defaults and to end generation)
```

Now generate a Product Entity, with properties for `description` (text), `image` (text) and `price` (float):

```bash
    $ php bin/console make:entity Product

     created: src/Entity/Product.php
     created: src/Repository/ProductRepository.php
     
     ... etc. etc.
```

## Defining the many-to-one relationship from Product to Category

We now edit our `Product` entity, declaring a property `category` that has a many-to-one relationship with entity `Category`. I.e., many products relate to one category.

Add the following field and setter in `/src/Entity/Product.php`:

```php

    class Product
    {

        ... properties and accessor methods for descrition / image / price ...

        /**
         * @ORM\ManyToOne(targetEntity="App\Entity\Category", inversedBy="products")
         * @ORM\JoinColumn(nullable=true)
         */
        private $category;

        public function getCategory(): Category
        {
            return $this->category;
        }

        public function setCategory(Category $category)
        {
            $this->category = $category;
        }
```

## How to allow `null` for a Product's category

We need to allow `null` for a Product's category:

- when it is first created (to generate a form the easy way)

- to allow a category to be removed from a Product


We need to allow our 'getter' to return `null` or a reference to a `Category`, so we change the return type to `?Category`:

```php
    // allow null - ?Category vs Category
    public function getCategory(): ?Category
    {
        return $this->category;
    }
```

We need set the default value for our 'setter' to `null`:

```php
    // default Category to null
    public function setCategory(Category $category = null)
    {
        $this->category = $category;
    }
```

This 'nullable' parameter/return value is one of the new features from PHP 7.1 onwards:

- [PHP.net guide to migrating to PHP 7.1](http://php.net/manual/en/migration71.new-features.php)

## Adding the optional one-to-many relationship from Category to Product

Each `Category` relates to many `Products`. Symfony with Doctrine makes it very easy to get an array of `Product` objects for a given `Category` object, without having to write any queries. We just declare a `products` property in gthe `Category` entity class, and use annotations to declare that it is the recipricol one-to-many relationship with entity `Product`.

Add the following field and setter in `/src/Entity/Product.php` (don't forget to add the `use` statement for class `ArrayCollection`):

```php
    use Doctrine\ORM\Mapping as ORM;
    use Doctrine\Common\Collections\ArrayCollection;

    /**
     * @ORM\Entity(repositoryClass="App\Repository\CategoryRepository")
     */
    class Category
    {
        ... properties and accessor methods for name ...

        /**
         * @ORM\OneToMany(targetEntity="App\Entity\Product", mappedBy="category")
         */
        private $products;

        public function __construct()
        {
            $this->products = new ArrayCollection();
        }
```

## Create and migrate DB schema

Configure your `.env` database settings:

```
    DB_USER=root
    DB_PASSWORD=pass
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_NAME=web7
    DATABASE_URL=mysql://${DB_USER}:${DB_PASSWORD}@${DB_HOST}:${DB_PORT}/${DB_NAME}
```

Generate the database, and migrations and migrate:

```
    $ php bin/console doctrine:database:create
    $ php bin/console doctrine:migrations:diff
    $ php bin/console doctrine:migrations:migrate
```

## Generate CRUD for Product and Category

Then generate CRUD for this entity (i.e. a ProductController and some templates in `/templates/product/`):

```bash
    $ php bin/console make:crud Product
    $ php bin/console make:crud Category
```

## Add Category selection in Product form

Our generated CRUD for Product creates a Symfony form using method `buildForm(...)` in generated form class `/src/Form/ProductType`:

```php
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('description')
            ->add('image')
            ->add('price')
            ->add('category');
    }
```

We need to refine this form builder a declaration that the `Category` to be associated with this Product can be set in the form. So we need to refine the `category` property to our builder as follows:

```php
    ->add('category', EntityType::class, [
              // list objects from this class
              'class' => 'App:Category',

              // use the 'Category.name' property as the visible option string
              'choice_label' => 'name',
          ]);
```

This references the `EntityType` class, so we need to add a `use` statement for this class:

```php
    use Symfony\Bridge\Doctrine\Form\Type\EntityType;
```

So the full listing for our updated `ProductType` class is:

```php
    namespace App\Form;
    
    use App\Entity\Product;
    use Symfony\Component\Form\AbstractType;
    use Symfony\Component\Form\FormBuilderInterface;
    use Symfony\Component\OptionsResolver\OptionsResolver;
    
    use Symfony\Bridge\Doctrine\Form\Type\EntityType;
    
    
    class ProductType extends AbstractType
    {
        public function buildForm(FormBuilderInterface $builder, array $options)
        {
            $builder
                ->add('description')
                ->add('image')
                ->add('price')
                ->add('category', EntityType::class, [
                    // list objects from this class
                    'class' => 'App:Category',
    
                    // use the 'Category.name' property as the visible option string
                    'choice_label' => 'name',
                ]);        ;
        }
    
        public function configureOptions(OptionsResolver $resolver)
        {
            $resolver->setDefaults([
                'data_class' => Product::class,
            ]);
        }
    }
```

## Add small and large item Category

Let's create two categories:

- `small items`

- `large items`

You could run the server and manually created these at CRUD page `/category/new`. Alterntively you could create a `CategoryFixtures` class to automatially add these to the database:

```php
    namespace App\DataFixtures;
    
    use Doctrine\Bundle\FixturesBundle\Fixture;
    use Doctrine\Common\Persistence\ObjectManager;
    use App\Entity\Category;
    
    class CategoryFixtures extends Fixture
    {
        public function load(ObjectManager $manager)
        {
            $cat1 = new Category();
            $cat1->setName('small items');
    
            $cat2 = new Category();
            $cat2->setName('large items');
    
            $manager->persist($cat1);
            $manager->persist($cat2);
    
            $manager->flush();
        }
    }
```

## Drop-down menu of categories when creating/editing `Product`s

Now we are automatically given a drop-down list of `Category` items to choose from when we visit `/product/new` to create a new `Product` object. See Figure \ref{category_dropdown}.

![Screenshot of Category dropdown for new Product form. \label{category_dropdown}](./03_figures/part07_associations/1_new_product.png)


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

So we can update the Product list Twig template to show Category as follows (`/templates/product/index.html.twig`):

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
        <th>Cateogry</th>
        <td>{{ product.category.name }}</td>
    </tr>
```

See Figure \ref{category_dropdown} to see Category  for each Product in the list.

![Screenshot of list of Products with their Category names. \label{product_list_category}](./03_figures/part07_associations/2_list_product_categories.png)

## toString() method

It is a good idea to have a default `__toString()` method in our `Category` Entity class, so we can write `product.category`. So add the following method to Entity class `src/Category.php`:

```php
    public function __toString()
    {
        return $this->name;
    }
```

Now change the `/templates/product/show.html.twig` template to just output `product.category`:

```twig
    <tr>
        <th>Cateogry</th>
        <td>{{ product.category }}</td>
    </tr>
```


<!--
## Linking objects in Symfony

First, let's get rid of any existing `Product` records in the database:

```bash
    $ php bin/console doctrine:query:sql 'delete from product where true'
```

Now we'll modify our create product controller method, to (for now) always create a new `Category` with name `default`, and link new `Product` record to that category.
So we edit `/src/Controller/ProductController.php` as follows:

```php
    public function createAction($description, $price)
    {

        // create new product object
        $product = new Product();
        $product->setDescription($description);
        $product->setPrice($price);

        // this will do for now
        $category = new Category();
        $category->setName('deafult');

        $product->setCategory($category);

        // persist (save/store) this object's contents to the database
        $em = $this->getDoctrine()->getManager();
        $em->persist($product);
        $em->persist($category);
        $em->flush();


        return $this->redirectToRoute('product_show', [
            'id' => $product->getId()
        ]);
    }
```


Figures \ref{new_products} shows a screenshot of products with their category id.

![Screenshot of MySQLWorkbench listing products with `category_id`. \label{new_products}](./03_figures/lab05_relationships/1_product_list.png)

We can also display the `Category` name in our `Product` show Twig template.
Update `/templates/product/show.html.twig` as follows:

```twig
    {% extends 'base.html.twig' %}

    {% block body %}
        <h1>Product SHOW page</h1>

        <p>
            id = {{ product.id }}
            <br>
            description = {{ product.description }}
            <br>
            price = &euro; {{ product.price }}
            <br>
            category = {{ product.category.name }}
        </p>
    {% endblock %}
```

Twig follows getter methods by just the the property name. So `{{ product.category.name }}` is equivalent to PHP `$product->getCategory()->getName()`!

## Problem - new category record for every new Product record

There is a problem with our code through: a new `default` `Category` record is created for every new `Product` record in our database.
Figures \ref{duplicate_categories} shows a screenshot of products with their category id.

![Screenshot of MySQLWorkbench showing duplicate categories. \label{duplicate_categories}](./03_figures/lab05_relationships/2_categories.png)

Learning about **fixtures** will help solve this problem ...
 -->


## Setup relationship via `make`

The new improved command line `make` tool can actually do a lot of the above work for us automatically, by defining a `category` property when making Entity class `Product`, with a `Field type` of `relation` (rather than string etc.).

```bash
    ... (make entity Product)
    
    New property name (press <return> to stop adding fields):
    > category
    
    Field type (enter ? to see all types) [string]:
    > relation
    
    What class should this entity be related to?:
    > Category
    
    Relation type? [ManyToOne, OneToMany, ManyToMany, OneToOne]:
    > ManyToOne
    
    Is the Product.category property allowed to be null (nullable)? (yes/no) [yes]:
    > no
    
    Do you want to add a new property to Category so that you can access/update
    getProducts()? (yes/no) [yes]:
    > yes
    
    New field name inside Category [products]:
    > products
    
    Do you want to automatically delete orphaned App\Entity\Product objects
    (orphanRemoval)? (yes/no) [no]:
    > no
    
     to stop adding fields):
    >
    (press enter again to finish)
```

Learn more in the Symfony documentation:

- [https://symfony.com/doc/current/doctrine/associations.html](https://symfony.com/doc/current/doctrine/associations.html)