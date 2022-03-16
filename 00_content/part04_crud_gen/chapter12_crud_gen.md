
# CRUD controller and templates generation\label{crud_gen_chapter}


## Symfony's CRUD generator (project `crud01`)

Symfony has a powerful CRUD generator. Given just an Entity class, the maker-builder can now generate for you:

- controller class, for CRUD routes (list / show / new / edit / delete)

- Form class for the entity

- templates for: list / show / new / edit / delete

## Preparation - new project with fixtures package

Create a new project:

```bash
     symfony new --webapp crud01
```

Now `cd` into the project in a terminal, and add the fixtures package:

```bash
     composer req orm-fixtures
```

## Remove encore entry points in base Twig template

If you are not using WebPack encore, remove the `encore_entry_` lines inside the `stylesheets` and `javascripts` blocks in the base template (`/templates/base.html.twig`). So these blocks will be empty in this parent template file:

```twig
    {% block stylesheets %}
    {% endblock %}

    {% block javascripts %}
    {% endblock %}
```

## Generating new Entity class `Category`

We'll start by creating a simple entity for the name of the category of products (hardware or tools).

Use the Symfony make tool to generate a new entity class `Category`:

```bash
     symfony console make:entity Category
```

At the interactive command line add a single field  `name` with defaults of string, length, and not null.


You should now have a basic entity `/src/Entity/Category.php`:

```php
    #[ORM\Entity(repositoryClass: CategoryRepository::class)]
    class Category
    {
        #[ORM\Id]
        #[ORM\GeneratedValue]
        #[ORM\Column(type: 'integer')]
        private $id;

        #[ORM\Column(type: 'string', length: 255)]
        private $name;
        
        ... getters and setters 
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


## Generating CRUD for a new Entity class

You can now create Symfony CRUD for the `Category` entity as follows (in this example the  is used), and accept the default new controller name of `CategoryController`:

```bash
    $ symfony console make:crud Category

    Choose a name for your controller class (e.g. CategoryController) [CategoryController]:
     >

     created: src/Controller/CategoryController.php
     created: src/Form/CategoryType.php
     created: templates/category/_delete_form.html.twig
     created: templates/category/_form.html.twig
     created: templates/category/edit.html.twig
     created: templates/category/index.html.twig
     created: templates/category/new.html.twig
     created: templates/category/show.html.twig
    Success!
       
    Next: Check your new CRUD by going to /category/
```

With the single command above Symfony will generate a CRUD controller (`CategoryController`) and also create a directory containing Twig templates (`/templates/category/index.html.twig` etc.). The list of new files is:

```
    /src/Controller/CategoryController.php

    /src/Form/CategoryType.php

    /templates/category/_form.html.twig
    /templates/category/_delete_form.html.twig
    /templates/category/edit.html.twig
    /templates/category/index.html.twig
    /templates/category/new.html.twig
    /templates/category/show.html.twig
```

The list of new (attribute-defined routes):

```
    /category --> CategoryController->index(...)
    /category/new --> CategoryController->new(...)
    /category/show/{id} --> CategoryController->show(...)
    /category/delete/{id} --> CategoryController->delete(...)
```

## The generated routes

Let's see the new routes generated magically for us:

```bash
     -------------------------- ---------- -------- ------ ---------------------------------------
      Name                       Method     Scheme   Host   Path
     -------------------------- ---------- -------- ------ ---------------------------------------
      _preview_error             ANY        ANY      ANY    /_error/{code}.{_format}
      ... more "_" prefixed debug console routes

      app_category_index             ANY        ANY      ANY    /category/
      app_category_new               GET|POST   ANY      ANY    /category/new
      app_category_show              GET        ANY      ANY    /category/{id}
      app_category_edit              GET|POST   ANY      ANY    /category/{id}/edit
      app_category_delete            DELETE     ANY      ANY    /category/{id}
```


NOTE: The **sequence** of these routes is important . a `GET` request with a URL looking like this: `/category/1` should be matched to the show action, i.e. `/category/{id = 1}`. But a URL like this `/category/new` we want to match with the action. If the show route attempts to match **before** the new route, then `/category/new` is matched to the show route as `/category/{id = new}`, which will then throw a 404 error, since `new` is not a valid `id` for a database `Category` object.

Once solution is to ensure the new action method appears **before** the show action method in our controller class. If you don't like this solution (I'm not sure myself), then another solution is to design URL routes that cannot get mixed up like this - but that means adding **verbs** for every every route. E.g. our category routes could be defined as follows:

```bash
     -------------------------- ---------- -------- ------ ---------------------------------------
      Name                       Method     Scheme   Host   Path
     -------------------------- ---------- -------- ------ ---------------------------------------
      app_category_index             ANY        ANY      ANY    /category/list
      app_category_new               GET|POST   ANY      ANY    /category/new
      app_category_show              GET        ANY      ANY    /category/show/{id}
      app_category_edit              GET|POST   ANY      ANY    /category/edit/{id}
      app_category_delete            DELETE     ANY      ANY    /category/delete/{id}
      ...
```

So, for each request, the entity `category` is followed by its action verb (`list`, `show`, `new` etc.), and then finally, if required an `{id}` parameter. This approach has the advantage of being simple and unambiguous (the sequence of methods in our controller class no longer matters), But (a) it breaks with common conventions in routes in Symfony projects, and (b) it means the URLs are getting longer, and simple, short URLs are one aim (benefit!) of a well designed web framework.

But for **your** personal projects, choose a route pattern scheme that **you** prefer, so this **verb** approach might be something you are happier with. It would also mean you could use  `GET` metod for **delete** requests, rather than emulating a `DELETE` HTTP request ...


## The generated CRUD controller

A controller class was geneated for Category objects in `/src/Controller/CategoryController.php`.
Let's first look at the namespaces and class declaration line:

```php
    namespace App\Controller;

    use App\Entity\Category;
    use App\Form\CategoryType;
    use App\Repository\CategoryRepository;
    use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Component\Routing\Annotation\Route;

    #[Route('/category')]
    class CategoryController extends AbstractController
```

Above we see a set of `use` statements, and then an interesting class comment. The `@Route` annotation comment declares a route 'prefix' which will at the beginning of any `@Route` annotations for individual controller methods. So, for example, the new action will have the route `/category/new`.


If we look in directory `/templates/category/` we'll see the following generated main templates:

```
    edit.html.twig
    index.html.twig
    new.html.twig
    show.html.twig
```

and the 2 partial templates (that are included in other pages):

```
    _delete_form.html.twig
    _form.html.twig
```

Note that all these generated templates extend Twig class `base.html.twig`.

## The generated index (a.k.a. list) controller method

Below we can see the code for `index()` that retrieves and then passes an array of `Category` objects to template `/templates/category/index.html.twig.

```php
    #[Route('/', name: 'app_category_index', methods: ['GET'])]
    public function index(CategoryRepository $categoryRepository): Response
    {
        return $this->render('category/index.html.twig', [
            'categories' => $categoryRepository->findAll(),
        ]);
    }
```

Note how this uses the 'magic' of the Param Converter to get a reference to the `CategoryRepository` as a method parameter. This makes it a one-liner to `findAll()` objects in the database and pass them on to the Twig template.

If you prefer, you can re-write the last statement in the more familiar form:

```php
    $argsArray = [
        'categories' => $categoryRepository->findAll()
    ];

    $template = 'category/index.html.twig';
    return $this->render($template, $argsArray);
```

Twig template `category/index.html.twig` loops through array `categories`, wrapping HTML table row tags around each entity's content:

```html
    {% for category in categories %}
        <tr>
            <td>{{ category.id }}</td>
            <td>{{ category.name }}</td>
            <td>
                <a href="{{ path('app_category_show', {'id': category.id}) }}">show</a>
                <a href="{{ path('app_category_edit', {'id': category.id}) }}">edit</a>
            </td>
        </tr>
    {% else %}
        <tr>
            <td colspan="3">no records found</td>
        </tr>
    {% endfor %}
```

## Create 4 categories

Visit `/category/new` and create the following 3 categories:

- hardware
- garden
- brick-a-brack

## Style the list of entities

Let's create a CSS file for table borders and padding in a new file `/public/css/table.css`;

```css
    table, tr, td, th {
        border: 0.1rem solid black;
        padding: 0.5rem;
    }
```

Remember in `/templates/base.html.twig` there is a block for style sheets:

```html
    <head>
        <meta charset="UTF-8">
        <title>{% block title %}Welcome!{% endblock %}</title>
        {% block stylesheets %}{% endblock %}
    </head>
```

So now we can edit template `category/index.html.twig` to add a stylesheet block import of this CSS stylesheet:

```html
    {% block stylesheets %}
        <style>
            @import '/css/table.css';
        </style>
    {% endblock %}
```


Figure \ref{app_category_index} shows a screenshot of how our list of categories looks, rendered by the `categories/index.html.twig` template.


![List of categories in HTML table. \label{app_category_index}](./03_figures/part04_crud/01_crud_table_styles.png){ width=50%}


## The generated `new()` method

The method and Twig template for a new `Category` work just as you might expect. For `GET` requests (and invalid `POST` submissions) a form will be displayed. Upon valid `POST` submission the `$category` object populated with the form data will be persisted to the database, and then the user will be redirected to the `list` action to see all entities, including the new one:

```php
    #[Route('/new', name: 'app_category_new', methods: ['GET', 'POST'])]
    public function new(Request $request, CategoryRepository $categoryRepository): Response
    {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $categoryRepository->add($category);
            return $this->redirectToRoute('app_category_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('category/new.html.twig', [
            'category' => $category,
            'form' => $form,
        ]);
    }
```

Note that it redirects to the edit method (`app_category_index`) after a successful object creation and saving to the database.

```php
    return $this->redirectToRoute('app_category_index', [], Response::HTTP_SEE_OTHER);
```


## The generated `show()` method

Initially, the generated 'show' method looks just as we might write ourselves:

```php
    #[Route('/{id}', name: 'app_category_show', methods: ['GET'])]
    public function show(Category $category): Response
    {
        return $this->render('category/show.html.twig', [
           'category' => $category,
        ]);
    }
```

But looking closely, we see that while the route specifies parameter `{id}`, the method declaration species a parameter of `Category $category`. Also the code in the method makes no reference to the `Category` entity repository. So by some **magic** the numeric 'id' in the request path has used to retrieve the corresponding `Category` record from the database!

This magic is the work of the Symfony 'Param Converter'. Also, of course, if there is no record found in table `category` that corresponds to the received 'id', then a 404 not-found-exception will be thrown.

Learn more about the 'param converter' at the Symfony documentation pages:

- [https://symfony.com/bundles/SensioFrameworkExtraBundle/current/annotations/converters.html](https://symfony.com/bundles/SensioFrameworkExtraBundle/current/annotations/converters.html)

## The generated `edit()`  method

The 'edit'  generated method is as you might expect. The edit method creates a form, and also include code to process valid submission of the edited entity. 

Note that it redirects to itself upon successful save of edits.


```php
    #[Route('/{id}/edit', name: 'app_category_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Category $category,
        CategoryRepository $categoryRepository): Response
    {
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $categoryRepository->add($category);
            return $this->redirectToRoute('app_category_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('category/edit.html.twig', [
            'category' => $category,
            'form' => $form,
        ]);
    }
```

## The generated `delete()` method

The 'delete' method deletes the entity and redirects back to the list of categories for the 'index' action. Notice that the attribute states that this controller method is in response to `DELETE` method requests (more about this below).

```php
    #[Route('/{id}', name: 'app_category_delete', methods: ['POST'])]
    public function delete(Request $request, Category $category,
        CategoryRepository $categoryRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$category->getId(), $request->request->get('_token'))) {
            $categoryRepository->remove($category);
        }

        return $this->redirectToRoute('app_category_index', [], Response::HTTP_SEE_OTHER);
    }
```

The delete form is reached via a Twig include from the edit template (`templates/category/edit.html.twig`):

```twig
    {% extends 'base.html.twig' %}

    {% block title %}Edit Category{% endblock %}

    {% block body %}
        <h1>Edit Category</h1>

        {{ include('category/_form.html.twig', {'button_label': 'Update'}) }}

        <a href="{{ path('app_category_index') }}">back to list</a>

        {{ include('category/_delete_form.html.twig') }}
    {% endblock %}
```

If we actually look at the HTML source of this `_delete_form.html.twig` button-form, we can see that it is actually submitted with the HTTP `post` action, along with a hidden form field named `_method` with the value `DELETE`.

This kind of approach means we can write our controllers as if they are responding to the full range of HTTP methods (`GET`, `POST`, `PUT`, `DELETE` and perhaps `PATCH`).

```html
    <form
        method="post"
        action="{{ path('app_category_delete', {'id': category.id}) }}"
        onsubmit="return confirm('Are you sure you want to delete this item?');"
    >
        <input type="hidden" name="_token" value="{{ csrf_token('delete' ~ category.id) }}">
        <button class="btn">Delete</button>
    </form>

```
