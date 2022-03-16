
# Saving and loading data from sessions

## Working with sessions in Symfony Controller methods (project `session02`)

First, since we are going to be using sessions, let's return our default `index()` controller method to pass no arguments to the Twig template. This is because any color variables will be stored in the session and set by other controllers:

```php
    #[Route('/', name: 'app_default')]
    public function index(): Response
    {
        $template = 'default/index.html.twig';
        $args = [
        ];
        return $this->render($template, $args);
    }
```

We don't need to explicitly start a session in Symfony, as soon as we try to read/write to the session a new session will automatically be started (if one doesn't already exist).

We access sessions through the **RequestStack**. So any controller with a method that needed to access the session needs to declare a private `RequestStack` variable, which we initialised with the Symfony param-converter via a constructor method:

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

Note - do **not** use any of the standard PHP command for working with sessions. Do all your Symfony work through the Symfony session API. So, for example, do not use either of these PHP functions:

```php
    session_start(); // ----- do NOT use this in Symfony -------
    session_destroy(); // ----- do NOT use this in Symfony -------
```

You can now set/get values in the session by making reference to `$session`, which we can get from the `RequestStack` object with this statement: ` $session = $this->requestStack->getSession()`.


## Symfony's session 'bags'

We've already met sessions - the Symfony 'flash bag', which stores messages in the session for one request cycle.

Symfony also offers a second kind of session storage, session 'attribute bags', which store values for longer, and offer a namespacing approach to accessing values in session arrays.

We store values in the attribute bag as follows using the `$session->set()` method:

```php
    $session->set('<key>', <value>);
```

See the Symfony session documentation page for other session related methods:

- [https://symfony.com/doc/current/components/http_foundation/sessions.html](https://symfony.com/doc/current/components/http_foundation/sessions.html)

## Storing colors array in the session bag

Here's how we store our colors array in the Symfony application session from our controllers:

```php
    // create colors array
    $colors = [
        'foreground' => 'blue', 'background' => 'pink'
    ];

    // store colours in session variable 'colours'
    $session = $this->requestStack->getSession();
    $session->set('colors', $colors);
```

Note - also learn how to 'unset' values when you learn to set them. We can clear everything in a session by writing:

```php
    $session = $this->requestStack->getSession();
    $session->clear();
```


## Storing values in the session in a controller action

Let's refactor `DefaultController` method `pinkBlue()` which has route `/pinkblue` with logic to  store colours in the session and then re-direct Symfony to the home page route:

```php
    #[Route('/pinkblue', name: 'pinkblue')]
    public function pinkblue(): Response
    {
        $colors = [
            'foreground' => 'blue', 'background' => 'pink'
        ];

        // store colours in session variable 'colours'
        $session = $this->requestStack->getSession();
        $session->set('colors', $colors);

        return $this->redirectToRoute('app_default');
    }
```


You can view session values in its session tab, as show in Figure \ref{session_profiler}.

![Homepage with session colours applied via CSS. \label{session_profiler}](./03_figures/part05_sessions/8_profiler_session_variable.png)


## Twig function to retrieve values from session 

Twig offers a function to attempt to retrieve a named value in the session:

```twig
    app.session.get('<attribute_key>')
```

If fact the `app` Twig variable allows us to read lots about the Symfony, including:

- request (`app.request`)

- user (`app.user`)

- session (`app.session`)

- environment (`app.environment`)

- debug mode (`app.debug`)

Read more about Twig `app` in the Symfony documentation pages:

- [https://symfony.com/doc/current/templating/app_variable.html](https://symfony.com/doc/current/templating/app_variable.html)

## Attempt to read `colors` array property from the session



We can store values in Twig variables using the `set <var> = <expression>` statement. So let's try to read an array of colours from the session named `colors`, and store in a local Twig variable names `colors`:

```html
    {% set colors = app.session.get('colors') %}
```
 
After this statement, `colors` either contains the array retrieved from the session, or it is `null` if no such variable was found in the session.

So we can test for `null`, and if `null` is the value of `colors` then we can set `colors` to our default (black/white) values:

```twig
    {# ------ attempt to read 'colors' from session ----- #}
    {% set colors = app.session.get('colors') %}

    {# ------ if 'null' then not found in session ----- #}
    {# ------ so set to black/white default values ----- #}
    {% if colors is null %}
        {# ------ set our default colours array ----- #}
        {% set colors = {
            'foreground': 'black',
            'background': 'white'
        }
        %}
    {% endif %}
```

So at this point we know `colors` contains an array, either from the session or our default values (black/white) set in the Twig template.

The full listing for our Twig template `default/index.html.twig` looks as follows: first part logic testing session, second part outputting details about the variables:

```twig
    {# ------ attempt to read 'colors' from session ----- #}
    {% set colors = app.session.get('colors') %}

    {# ------ if 'null' then no found in session ----- #}
    {% if colors is null %}
        {# ------ set our default colours array ----- #}
        {% set colors = {
            'foreground': 'black',
            'background': 'white'
        }
        %}
    {% endif %}

    <ul>
        {% for property, color in colors %}
            <li>
                {{ property }} = {{ color }}
            </li>
        {% endfor %}
    </ul>

    <p>
        Hello World
    </p>
```

Finally, we can add another route method in our controller to **clear the session**, i.e. telling our site to reset to the default colors defined in our Twig template:

```php
    #[Route('/clear', name: 'default_colors')]
    public function defaultColors(): Response
    {
        $session = $this->requestStack->getSession();
        $session->clear();

        return $this->redirectToRoute('app_default');
    }
```



## Applying colours in HTML head `<style>` element (project `session03`)

Since we have an array of colours, let's finish this task logically by moving our code into `base.html.twig` and creating some CSS to actually set the foreground and background colours using these values.

So we remove the Twig code from template `index.html.twig`. So this template just adds our `Hello World` paragraph to the `body` block:

```twig
    {% extends 'base.html.twig' %}
    
    {% block title %}Hello Default Controller!{% endblock %}
    
    {% block body %}
    <p>
        Hello World
    </p>
    {% endblock %}
```
 
 
We'll place our (slightly edited) Twig code into `base.html.twig` as follows. Add the following **before** we start the HTML doctype etc.

```html
    {# ------ attempt to read 'colors' from session ----- #}
    {% set colors = app.session.get('colors') %}
    
    {# ------ if 'null' then no found in session ----- #}
    {% if colors is null %}
        {# ------ set our default colours array ----- #}
        {% set colors = {
            'foreground': 'black',
            'background': 'white'
        }
        %}
    {% endif %}
    ... DOCTYPE and HTML for page ...
```

So now we know we have our Twig variable `colors` assigned values (either from the session, or from the defaults. Now we can update the `<head>` of our HTML to include a new `body {}` CSS rule, that pastes in the values of our Twig array `colors['foreground']` and `colors['background']`:

```
    ... Twig to set default colors array (from above)
    <!DOCTYPE html>
    <html>
        <head>
            <meta charset="UTF-8">
            <title>{% block title %}Welcome!{% endblock %}</title>

            <style>
                @import '/css/flash.css';
                body {
                    color: {{ colors['foreground'] }};
                    background-color: {{ colors['background'] }};
                }
            </style>
        ...
```


Figure \ref{colours_css_index} shows our text and background colours applied to the CSS of the website homepage.

![Homepage with session colours applied via CSS. \label{colours_css_index}](./03_figures/part05_sessions/9_css_source.png)


## Testing whether an attribute is present in the current session

Before we work with a session attribute in a PHP controller method, we may wish to test whether it is present. We can test for the existance of an attribute in the session bag as follows:

```php
    if($session->has('<key>')){
         //do something
     }
```

## Removing an item from the session attribute bag

To remove an item from the session attribute bag write the following:

```php
    $session->remove('<key>');
```

## Clearing all items in the session attribute bag

To remove all items from the session attribute bag write the following:

```php
    $session->clear();
```



