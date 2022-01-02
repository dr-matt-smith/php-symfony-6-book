
# First steps

## What we'll make (`basic01`)

See Figure \ref{new_home_page} for a screenshot of the new homepage we'll create in our first project (after some  setup steps).

![New home page.\label{new_home_page}](./03_figures/lab02/5_twig_page.png)

There are 3 things Symfony needs to serve up a page:

1. a route
2. a controller class and method
3. a Response object to be returned to the web client

The first 2 can be combined, through the use of 'attributes' , which declare the route in a line beginning `#` immediately before the controller method defining the 'action' for that route. See this example:


```php
    #[Route('/', name: 'homepage')]
    public function indexAction()
    {
        ... build and return Response object here ...
    }
```

For example the code below defines:

- a attribute Route comment for URL pattern `/` (i.e. website route)

    -- ```#[Route('/', name: 'homepage')]```

    -- the Symfony "router" system attempts to match pattern  `/` in the URL of the HTTP Request received by the server

- controller method `indexAction()`

    -- this method will be involved if the route matches

    -- controller method have the responsibility to create and return a Symfony `Response` object

- note, Symfony allows us to declare an internal name for each route (in the example above `homepage`)

    -- we can use the internal name when generating URLs for links in out templating system

    -- the advantage is that the route is only defined once (in the annotation comment), so if the route changes, it only needs to be changed in one place, and all references to the internal route name will automatically use the updated route

    -- for example, if this homepage route was changed from `/` to `/default` all URls generated using the `homepage` internal name would now generated `/default`


## Create a new Symfony project

1. Create new Symfony project (and then `cd` into it):

    ```bash
		$ symfony new --full project01

		* Creating a new Symfony project with Composer
        ... etc. ...

		[OK] Your project is now ready in /Users/matt/Documents/Books/php-symfony-6-book/codes/part01/project01

        $ cd basic01
    ```

1. Check this vanilla, empty project is all fine by running the web sever and visit website root at `http://localhost:8000/`:

```bash
    $ symfony serve
       Tailing Web Server log file (/Users/matt/.symfony/log/ec56398112e31dba20d3fec928509d0cec5c3764.log)
        Tailing PHP-FPM log file (/Users/matt/.symfony/log/ec56398112e31dba20d3fec928509d0cec5c3764/53fb8ec204547646acb3461995e4da5a54cc7575.log)
        WARNING read /Users/matt/.symfony/var/ec56398112e31dba20d3fec928509d0cec5c3764: is a directory
                                                                                                            
        [OK] Web server listening                                                                                              
          The Web server is using PHP FPM 8.1.1                                                                             
          https://127.0.0.1:8000                                                                                            
                               
        [Web Server ] Jan  2 18:53:06 |INFO   | PHP    listening path="/usr/local/Cellar/php/8.1.1/sbin/php-fpm" php="8.1.1" port=52407
        [PHP-FPM    ] Jan  2 18:53:06 |NOTICE | FPM    ready to handle connections 
                                                                                                                        
       // Quit the server with CONTROL-C.
```

Figure \ref{default_page} shows a screenshot of the default page for the web root (path `/`), when we have no routes set up and we are in development mode (i.e. our `.env` file contains `APP_ENV=dev`).

![Screenshot default Symfony 4 page for web root (when no routes defined). \label{default_page}](./03_figures/chapter02/0_default_page_sf.png)

## List the routes

There should not be any (non-debug) routes yet. All routes starting with an underscore `_` symbol are debugging routes used by the verye useful Symfony profiler - this creates the information footer at the bottom of our pages when we are developing Symfony applications.

 but let's check at the console by typing `php bin/console debug:router`:

```
    $ php bin/console debug:router
         -------------------------- -------- -------- ------ ----------------------------------- 
          Name                       Method   Scheme   Host   Path                               
         -------------------------- -------- -------- ------ ----------------------------------- 
          _wdt                       ANY      ANY      ANY    /_wdt/{token}                      
          _profiler_home             ANY      ANY      ANY    /_profiler/                        
          _profiler_search           ANY      ANY      ANY    /_profiler/search                  
          _profiler_search_bar       ANY      ANY      ANY    /_profiler/search_bar              
          _profiler_phpinfo          ANY      ANY      ANY    /_profiler/phpinfo                 
          _profiler_search_results   ANY      ANY      ANY    /_profiler/{token}/search/results  
          _profiler_open_file        ANY      ANY      ANY    /_profiler/open                    
          _profiler                  ANY      ANY      ANY    /_profiler/{token}                 
          _profiler_router           ANY      ANY      ANY    /_profiler/{token}/router          
          _profiler_exception        ANY      ANY      ANY    /_profiler/{token}/exception       
          _profiler_exception_css    ANY      ANY      ANY    /_profiler/{token}/exception.css   
          _preview_error             ANY      ANY      ANY    /_error/{code}.{_format}           
         -------------------------- -------- -------- ------ ----------------------------------- 


```

NOTE:

- you can usually shorten the Symfony CLI commands to 1 of 2 letters, e.g. `debug:router` could be written `de:ro` ...

The only routes we can see all start with an underscore (e.g. `_preview_error`), so no application routes have been declared yet ...

## Create a controller

We could write a new class for our homepage controller, but ... let's ask Symfony to make it for us. Typical pages seen by non-logged-in users like the home page, about page, contact details etc. are often referred to as 'default' pages, and so we'll name the controller class for these pages our `DefaultController`.

1. Tell Symfony to create a new homepage (default) controller. A since a class will be created starting with the controller name, ensure your controlle rname starts with a CAPITAL letter, e.g. `Default` not `default`:

```bash
    $ php bin/console make:controller Default

         created: src/Controller/DefaultController.php
         created: templates/default/index.html.twig
        
          Success! 
        
         Next: Open your new controller class and add some pages!
```

Symfony controller classes are stored in directory `/src/Controller`. We can see that a new controller class has been created 
named `DefaultController.php` in folder `/src/Controller`.

A second file was also created, a view template file `templates/default/index.html.twig`,

Look inside the generated class `/src/Controller/DefaultController.php`. It should look something like this:

```php
    <?php    
    namespace App\Controller;

    use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Component\Routing\Annotation\Route;
    
    class DefaultController extends AbstractController
    {
        #[Route('/default', name: 'default')]
        public function index(): Response
        {
            return $this->render('default/index.html.twig', [
                'controller_name' => 'DefaultController',
            ]);
        }
    }
```

This default controller uses a **Twig** template to return an HTML page:

```twig
  {% extends 'base.html.twig' %}
  
  {% block title %}Hello DefaultController!{% endblock %}
  
  {% block body %}
  <style>
      .example-wrapper { margin: 1em auto; max-width: 800px; width: 95%; font: 18px/1.5 sans-serif; }
      .example-wrapper code { background: #F5F5F5; padding: 2px 6px; }
  </style>
  
  <div class="example-wrapper">
      <h1>Hello {{ controller_name }}! TICK</h1>
  
      This friendly message is coming from:
      <ul>
          <li>Your controller at <code><a href="{{ '/Users/matt/Documents/Books/php-symfony-6-book/codes/part01/basic01/src/Controller/DefaultController.php'|file_link(0) }}">src/Controller/DefaultController.php</a></code></li>
          <li>Your template at <code><a href="{{ '/Users/matt/Documents/Books/php-symfony-6-book/codes/part01/basic01/templates/default/index.html.twig'|file_link(0) }}">templates/default/index.html.twig</a></code></li>
      </ul>
  </div>
  {% endblock %}
```

Let's 'make this our own' by changing the contents of the Response returned to a simple text response. Do the following:

- comment-out the body of the `index()` method

- at the top of the class add a `use` statement, so we can make use of the Symfony HTTFoundation class `Response`

    `use Symfony\Component\HttpFoundation\Response;`

- write a new body for the `index()` method to output a simple text message response:

    ```php
        return new Response('Welcome to your new controller!');
    ```

So the listing of your `DefaultController` should look as follows:
```php
<?php 
    namespace App\Controller;

    use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Component\Routing\Annotation\Route;
    
    class DefaultController extends AbstractController
    {
        #[Route('/default', name: 'default')]
        public function index(): Response
        {
            return new Response('Welcome to your new controller!');
    //        return $this->render('default/index.html.twig', [
    //            'controller_name' => 'DefaultController',
    //        ]);
        }
    }
```


## Run web server to visit new default route

Run the web sever and visit the home page at `http://localhost:8000/`.

But we see that default Symfony welcome page, not our custom response text message!

Since we **have** defined a route, we don't get the default page any more. However, since we named our controller `Default`, then this is the route that was defined for it:


```bash
  Name                       Method   Scheme   Host   Path
 -------------------------- -------- -------- ------ -----------------------------------
  _...(all those debug routes starting with _ )
  default                    ANY      ANY      ANY    /default
```

If we look more closely at the generated code, we can see this route `/default` in the **attribute** preceding controller method `index()` in `src/Controllers/DefaultController.php`

```php
    #[Route('/default', name: 'default')]
```

So visit `http://localhost:8000/default` instead, to see the page generated by our `DefaultController->index()` method.

NOTE:

- if you still don't see our custom welcome page, then try first clearing the 'cache' to see the result of code changes we have just made. To speed things up Syfony uses a cache (memory) of recent Responses - but if you've made code chance the cached pages and routes may be out of date...

- to clear the cache using a Symfony CLI comment type `php app/console cache:clear`

- to clear the cache by deleting the files themselves, DELETE the `/var` folder
  
  - you can safely delete this folder at any time (unless you are using SQLite and storing your DB files there...)

Figure \ref{generated_default} shows a screenshot of the message created from our generated default controller method.

![Screenshot of generated page for URL path `/default`. \label{generated_default}](./03_figures/part01/2_default_page_no_twig.png)


## Other types of Response content

We could also have asked our Controller function to return JSON rather than text. We can create JSON either using Twig,
 or with the inherited `->json(...)` method. For example, try replacing the body of your `index()` method with the following:

```php
        public function index()
        {
            return $this->json([
                'name' => 'matt',
                'age' => '21 again!',
            ]);
        }
```


## The default Twig page

If we return our `index()` method back to what was first automatically generated for us, we can see an HTML page in our browser that is output from the Twig template:

```php
    public function index()
    {
        return $this->render('default/index.html.twig', [
            'controller_name' => 'DefaultController',
        ]);
    }
```


Figure \ref{generated_twig_default} shows a screenshot of the Twig HTML page that was automatically generated.

![Screenshot of generated Twig page for URL path `/default`. \label{generated_twig_default}](./03_figures/part01/3_defaultFromTwig.png)



