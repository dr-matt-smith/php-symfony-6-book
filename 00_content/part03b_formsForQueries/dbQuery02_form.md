# Custom database queries

## Search Form for exact property value (project `query02`)

Searching by having to type values in the URL isn't ideal. So let's add an HTML form in the list of projects page, allowing users to enter the category that way.  Figure \ref{query6}illustrates what we are going to create.

![Form to search for category. \label{query6}](./03_figures/part03/q6_simpleForm.png){width=80%}

## The form in Twig template

Let's write the HTML code for the submission form for the Products list Twig template in `/templates/product/index.html.twig`.

At present we have a table `<thead>` with a row of column headers, and then a loop for each Product:

```twig    
    {% extends 'base.html.twig' %}
    
    {% block title %}Product index{% endblock %}
    
    {% block body %}
        <h1>Product index</h1>
    
        <table class="table">
            <thead>
                <tr>
                    <th>Id</th>
                    <th>Description</th>
                    <th>Price</th>
                    <th>Category</th>
                    <th>actions</th>
                </tr>
            </thead>
            <tbody>

    <<<< TABLE ROW WITH FORM TO GO HERE >>>>
    
            {% for product in products %}
                <tr>
                    <td>{{ product.id }}</td>    
```

We need to add a new table row between the table headers and the loop of Products:

```twig    
    <tr>
        <th></th>
        <th></th>
        <th></th>
        <th>
            <form action="{{ url('search_category') }}" method="post">
                <input name="category">
                <input type="submit">
            </form>
        </th>
        <th></th>
    </tr>

    {% for product in products %}
        <tr>
            ... as before
```

The row has empty cells, except for the 4th cell (the Category column), where we create a simple form. The form has:

- a method of `post`

- an action of `url('search_category')`

    - we'll have to create this new route in the `ProductController` to process submission of this form
    
- a text box named `category`

    - since this text box will appear in the Categorty column, we don't need to give a text prompt
    
    - the HTML default `<input>` type is `text`, so we don't need to specify this either 
    
- a Submit button

## Controller method to process form submission

Here is the new method in `ProductController` to process submission of this form - implementing the route `search_category`:

```php
    /**
     * @Route("/searchCategory", name="search_category", methods={"POST"})
     */
    public function searchCategory(Request $request): Response
    {
        $category = $request->request->get('category');

        if(empty($category)){
            return $this->redirectToRoute('product_index');
        }

        return $this->redirectToRoute('product_search', ['category' => $category]);
    }
```

The annotation comments specify the URL route `/searchCategory`, the internal route name `search_category`, and that we expect the request to be submitted using the `POST` method:

```php
    /**
     * @Route("/searchCategory", name="search_category", methods={"POST"})
     */
```

We need to extract the `category` variable submitted in the HTTP Request, so we need access to the Symfony `Request` object. The simnplest way to get a reference to this object is via the Symfony **param converter**, by adding `(Request $request)` as a method parameter. This means we now have Request object variable `$request` available to use in our method:

```php
    public function searchCategory(Request $request): Response
```

We can retrieve a value from the submitted `POST` variables int the request using the `get->` method naming the variable `category`. NOTE: In this instance `get` is a **getter** (accessor method) - not to be confused with the HTTP `GET` Request method...

```php
  $category = $request->request->get('category');
```


Finally, we can do some logic based on the value of form submitted variable `$category`. If this variable is an emptuy string, let's just redirect Symfonhy to run the method to list all products, route `product_index`:

```php
    if(empty($category)){
        return $this->redirectToRoute('product_index');
    }
```

If `$category` was **not** empty then we can redirect to our category search route, passing the value to this route:

```php
    return $this->redirectToRoute('product_search', ['category' => $category]);
```

## Getting rid of the URL search route

If we no longer wanted the URL search route, we could replace the final statement in our `searchCategory(...)` method to the following (and remove method `search(...)` altogether):

```php
    /**
     * @Route("/searchCategory", name="search_category", methods={"POST"})
     */
    public function searchCategory(Request $request): Response
    {
        $category = $request->request->get('category');

        if(empty($category)){
            return $this->redirectToRoute('product_index');
        }
    
        // if get here, not empty - so use value to search...
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

# Wildcard vs. exact match queries

## Search Form for partial description match (project `query03`)

Let's add a query form in the **description** column, so we need to edit Twig template  `/templates/product/index.html.twig`.

So we add a new search form in the second table header row, for internal route name `search_description`, and passing form variable `keyword`:

```twig
    <tr>
        <th></th>
        <th>
            <form action="{{ url('search_description') }}" method="post">
                <input name="keyword">
                <input type="submit">
            </form>
        </th>
        <th></th>
        <th>
            <form action="{{ url('search_category') }}" method="post">
                <input name="category">
                <input type="submit">
            </form>
        </th>
        <th></th>
    </tr>
```

Let's write the controller method to process our keyword form submission - edit `/src/ProductController.php` and add a new method:

```php
    /**
     * @Route("/searchDescription", name="search_description", methods={"POST"})
     */
    public function searchDescription(Request $request): Response
    {
        $keyword = $request->request->get('keyword');

        if(empty($keyword)){
            return $this->redirectToRoute('product_index');
        }

        // if get here, not empty - so use value to search...
        $productRepository = $this->getDoctrine()->getRepository('App:Product');
        $products =  $productRepository->findByDescription($keyword);

        $template = 'product/index.html.twig';
        $args = [
            'products' => $products,
            'keyword' => $keyword
        ];

        return $this->render($template, $args);
    }
```

The above is just like our category search - but does only work for an exact match of `keyword` with the value of the `description` property.

What we want is to implement something similar the SQL `LIKE "%wildcard%"` query, where a word **anywhere** in the text of the `description` property will be matched. 

## Custom queries in our Repository class

The solution is to write a custom query method `findByLikeDescription($keyword)` in our `ProductRepository` class as follows:

```php
    ...

    class ProductRepository extends ServiceEntityRepository
    {
        ...

        /**
         * @return Product[] Returns an array of Drill objects
         */
        public function findByLikeDescription($keyword)
        {
            return $this->createQueryBuilder('p')
                ->andWhere('p.description LIKE :keyword')
                ->setParameter('keyword', "%$keyword%")
                ->getQuery()
                ->getResult()
                ;
        }
    }
```

We can now use this method in our `ProductController` controller method `searchDescription(..)`:

```php
    // if get here, not empty - so use value to search...
    $productRepository = $this->getDoctrine()->getRepository('App:Product');

    $products =  $productRepository->findByLikeDescription($keyword);
```

See Figure \ref{query7} illustrates a wildcard search for any `Product` with description containing texgt `bag`.
    
![Form to wildcard search for description. \label{query7}](./03_figures/part03/q7_likeDescription.png){width=80%}

## Making wildcard a **sticky** form

You may have noticed that in `ProductController` method `searchDescription(...)` we are passing the value of `$keyword` as well as the array `$products` to our Twig template. 

This means that when the Product index page is called from our search method there will be an extra Twig variable `keyword` defined, which we can detect and use as a default value for our search form - so the user can **see** the wildcard value for which we are seeing a list of products:

```twig
    <form action="{{ url('search_description') }}" method="post">
        <input name="keyword"
               {% if keyword is defined %}
               value="{{ keyword }}"
               {% endif %}
        >
        <input type="submit">
    </form>
```

If there is no `keyword` Twig variable (it is not defined), then we don't add a `value` attribute to this form input.

NOTE: There is a difference between a variable existing and containing `NULL` versus no such variable being defined at all - ensure you write the correct test in Twig to distinguish between these differences...

