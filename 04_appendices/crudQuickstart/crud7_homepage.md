# Creating a homepage

## Adding a `default` controller

There are often several **static** pages for a website, that can be publicly viewed and don't display data from a database. Often we called these **default** pages. Examples include:

- home page
- about page
- privacy policy
- contact details

The final thing we'll do is add a home page to our website, and get rid of that annoying placeholder page we see. 

Also we'll learn a little about Symfony:

- routing (how Symfony decides WHAT do to based on the URL of the HTTP Request received)
- controllers (classes containing logic actions to create different Responses corresponding to different Requests from the user/web client)
- paths (routes can be named, making them easy to use to generate LINKS in other pages of the site)
- Twig templates (templates are the files used to **decorate** data and construct the final HTML (or whatever) Request content to be returned to the user/web client)

## Create a new `DefaultController`

Let's make a controller class for our default pages. Do the following:

1. at the terminal use the make tool to create a new `DefaultController`

```bash
        $ symfony console make:controller DefaultController
         created: src/Controller/DefaultController.php
         created: templates/default/index.html.twig
          Success! 

         Next: Open your new controller class and add some pages!
```

NOTE: We could have used the abbreviated `ma:co DefaultController`

We can see from the output that 2 files have been created (and a new folder `tempaltes/default`).

- class `DefaultController` is where we can write logic to process different requests from the user
- template folder and file `templates/default/index.html.twig` will create the HTML returned to the user/web client when the visit the homepage

## Customising the HTML template

Replaced the generated contents of `templates/default/index.html.twig` with the following simple home page main content:

```twig
    {% extends 'base.html.twig' %}
    
    {% block title %}My Great Website - home page{% endblock %}
    
    {% block body %}
    <h1>Home page</h1>
    <p>
        welcome to my great website !
    </p>
    {% endblock %}
```

But, if we visit `https://127.0.0.1:8000/` we stil see the placeholder page, welcoming us to Symofny, but with the message saying we haven't configured the homepage URL.

What happened ???/

## Enter the Symfony debugging tool

You may have noticed the debugging information bar along the bottom of the web pages. You should see there is a red `404` (HTTP not found error) message at the bottom left of the page - click this. See Figure  \ref{404_homepage_link}.


![Clicking error in the debugging footer.\label{404_homepage_link}](./03_figures/app_crud/6_debug_404.png){width=100%}

Figure  \ref{no_route_matches} shows what happens when we click the `Routing` details from the left-hand navigation column of the debug information. None of the **routes** of our website matched the URL of `/`. What we can notice is that the Symfony routing system tested against, and failed to match, a route named `default` which had a URL pattern of `/default`.

![No rounte, including `/default` matches URL.\label{no_route_matches}](./03_figures/app_crud/6_no_route_matches.png){width=100%}

If we look at the beginning of the `DefaultController` class we created (in `src/Controller`) we can see why our, intended, homepage route is not working as we wish:

```php
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

The special `#Route` PHP 8 attribute, just before method `index()` provides 2 arguments:

- `/default` - the URL pattern the HTTP Request is to be matched against
- `default` - the internal name of the route (useful for generating links dymamically in things like navbars and other Twig templates)

If you wish, you could visit `/default` to see our homepage! But let's fix things so we see our home page when the URL is `/`.

Let's fix this, so that the `index()` method of our `DefaultController` matches the URL homepage URL of `/`. Let's also name this route `homepage`:

```php
    #[Route('/', name: 'homepage')]
    public function index(): Response
    {
        $template = 'default/index.html.twig';
        $args = [];
        return $this->render($template, $args);
    }
```

Above we've changed the URL and route name parameters in the PHP 8 attribute: `#[Route('/', name: 'homepage')]`

We have also simplified the statements of the `index()` method, since there are just 2 arguments for the `render(...)` method of the Twig object property inside a controller class:

- the template name (Symfony knows to look inside directory `templates` for Twig templates)

- any arguments (such as objects or arrays of objects) to be passed to the Twig template

  - none for our hopmepage, so we can just pass an empty array as the second argument

Figure  \ref{homepage_screenshot} shows the homepage working when we visit `/`.

![Working homepage.\label{homepage_screenshot}](./03_figures/app_crud/6_homepage_screenshot.png){width=100%}








