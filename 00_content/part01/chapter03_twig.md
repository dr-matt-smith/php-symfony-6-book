
# Twig templating

## Customizing the Twig output (`basic02`)

Look at the generated code for the `index()` method of class `DefaultController`:

```php
    namespace App\Controller;
    
    use Symfony\...
    
    class DefaultController extends AbstractController
    {
        #[Route('/default', name: 'default')]
        public function index(): Response
        {
            return $this->render('default/index.html.twig', [
                'controller_name' => 'DefaultController',
            ]);
```

As you can see, the controller method now returns the output of method `$this->render(...)` rather than directly creating a `Response` object. With the Twig bundle added, each controller class now has access to the Twig `render(...)` method.

Figure \ref{generated_default_twig} shows a screenshot of the message created from our generated default controller method with Twig.

NOTE: The actual look of the default generated Twig content may be a little different (e.g. 19 Feb 2019 it now says `Hello DefaultController!`)...

![Screenshot of generated page for URL path `/default`. \label{generated_default_twig}](./03_figures/part01/3_defaultFromTwig.png)



## Specific URL path and internal name for our default route method

Let's change the URL path to the website root (`/`) and name the route `homepage` by editing the annotation comments preceding method `index()` in `src/Controllers/DefaultController.php`.


```php
    class DefaultController extends AbstractController
    {
        #[Route('/', name: 'homepage')]
        public function index(): Response
```

Now the route is as follows (from typing `symfony console de:ro`):
```bash
  Name                       Method   Scheme   Host   Path
 -------------------------- -------- -------- ------ -----------------------------------
  homepage                   ANY      ANY      ANY    /
```

Finally, let's replace that default message with an HTTP response that **we** have created - how about the message `hello there!`. We can generate an HTTP response by creating an instance of the `Symfony\Component\HttpFoundation\Response` class.

Luckily, if we are using a PHP-friendly editor like PHPStorm, as we start to type the name of a class, the IDE will popup a suggestion of namespaced classes to choose from.
Figure \ref{phpstorm_response} shows a screenshot of PHPStorm offering up a list of suggested classes after we have typed the letters `new Re`. If we accept a suggested class from PHPStorm, then an appropriate `use` statement will be inserted before the class declaration for us.

![Screenshot of PHPStorm IDE suggesting namespaces classes. \label{phpstorm_response}](./03_figures/lab02/4_phpstorm_class_suggester.png)


Here is a complete `DefaultController` class:
```php
    namespace App\Controller;

    use Symfony\Component\Routing\Annotation\Route;
    use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
    use Symfony\Component\HttpFoundation\Response;

    class DefaultController extends AbstractController
    {
        #[Route('/', name: 'homepage')]
        public function index(): Response
        {
            return new Response('Hello there!');
        }
    }
```


Figure \ref{hello_there} shows a screenshot of the message created from our `Response()` object.

![Screenshot of page seen for `new Response('hello there!')`. \label{hello_there}](./03_figures/lab02/3_hello_there.png)


## Clearing the cache

Sometimes, when we've added a new route, we still get an error saying the route was not found, or showing us out-of-date content. This can be a problem of the Symfony **cache**.

So clearing the cache is a good way to resolve this problem (you may get in the habit of clearing the cache each time you add/change any routes).

You can clear the cache in 2 ways:

1. Simply delete directory `/var/cache`

1. Use the CLI command to clear the cache:

    ```bash
        $ symfony console cache:clear
        
        // Clearing the cache for the dev environment with debug true                                                          
        [OK] Cache for the "dev" environment (debug=true) was successfully cleared.   
        
        $
    ```


## Let's create a nice Twig home page  

We are (soon) going to create Twig template in `templates/default/homepage.html.twig`. So we need to ask the `Twig` object in our Symfony project to create an HTTP response via its `render()` method. Part of the 'magic' of PHP Object-Orienteted inheritance (and the **Dependancy Injection** design pattern), is that since our controller class is a subclass of `Symfony\Bundle\FrameworkBundle\Controller\Controller`, then objects of our controller automatically have access to a `render(...)` method for an automatically generated Twig object.

In a nutshell, to output an HTTP response generated from Twig, we just have to specify the Twig template name, and relative location^[The 'root' of Twig template locations is, by default, `/templates`. To keep files well-organised, we should create subdirectories for related pages. For example, if there is a Twig template `/templates/admin/loginForm.html.twig`, then we would need to refer to its location (relative to `/templates`) as `admin/loginForm.html.twig`. ], and supply an array of any parameters we want to pass to the template.

So we can simply write the following to ask Symfony to generate an HTTP response from Twig's text output from rendering the template that can (will soon!) be found in `/tempaltes/default/homepage.html.twig`:

```php
        #[Route('/', name: 'homepage')]
        public function indexAction()
        {
            $template = 'default/index.html.twig';
            $args = [];
            return $this->render($template, $args);
        }
```

Now let's put our own personal content in that Twig template in `/templates/default/index.html.twig`!

- Replace the contents of file `index.html.twig` with the following:

    ```twig
        {% extends 'base.html.twig' %}
    
        {% block body %}
            <h1>Home page</h1>
    
            <p>
                welcome to the home page
            </p>
        {% endblock %}
    ```

Note that Twig paths searches from the Twig root location of `/templates`, not from the location of the file doing the inheriting, so do **NOT** write `{% extends 'default/base.html.twig' %}`...

Figure \ref{twig_page} shows a screenshot our Twig-generated page in the web browser.

![Screenshot of page from our Twig template. \label{twig_page}](./03_figures/lab02/5_twig_page.png)



