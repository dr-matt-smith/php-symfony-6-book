
# Introduction to Symfony sessions

## Create a new project from scratch (project `sessions01`)

Let's start with a brand new project to learn about Symfony sessions:

```bash
    $ composer create-project symfony/skeleton session01
```

Let's add to our project the Twig and annotations packages^[And add the server package to `--dev` if that's how you are testing locally.]:

```bash
    $ composer req --dev server make
    $ composer req twig annotations
```

It's also a good idea to also include the **Debug** pacakge:

```bash
    $ composer req --dev debug
```

## Default controller - hello world

Create a new Default controller that renders a Twig template to say `Hello World` to us.

So the controller should look as this (you can speed things up using `make` and then editing the created file). If editing a generated controlle, don't forget to change the route pattern from `/default` to the website root of `\` in the annotation comment
:
```php
    namespace App\Controller;

    use Symfony\Component\Routing\Annotation\Route;
    use Symfony\Bundle\FrameworkBundle\Controller\Controller;

    class DefaultController extends Controller
    {
        /**
         * @Route("/", name="default")
         */
        public function indexAction()
        {
            $template = 'default/index.html.twig';
            $args = [];
            return $this->render($template, $args);
        }
    }
```

Our home page default template `default/index.html.twig` can be this simple^[If we have a suitable HTML skeleton base template.]:

```twig
    {% extends 'base.html.twig' %}

    {% block body %}
    <p>
        Hello World
    </p>
    {% endblock %}
```

## Twig foreground/background colours (`sessions02`)

Let's start out Symfony sessions learning with the ability to store (and remember) foreground and background colours^[I'm not going to get into a colo(u)rs naming discussion. But you may prefer to just always use US-English spelling (*sans* 'u') since most computer language functions and variables are spelt the US-English way]. 

First, let's just pass in a Twig variable from our controller, so that we can write some Twig to work with these variables. Later we'll not receive this variable from the controller, instead we'll use Twig to search for colors in the **session** and set these variables accordingly. But for now, we'll pass a variable from our controller to Twig: 

- `colors`: an array holding 2 colors for foreground (text color) and background color

    ```php
    $colors = [
        'foreground' => 'white',
        'background' => 'black'
    ];
    ```

So our controller needs to create this variable and pass it on to Twig:

```php
    public function index()
    {
        $colors = [
            'foreground' => 'white',
            'background' => 'black'
        ];

        $template = 'default/index.html.twig';
        $args = [
            'colors' => $colors,
        ];
        return $this->render($template, $args);
    }
```

Next let's add some HTML in our `default/index.html.twig` page to display the value of our 2 stored values.


```html
    <ul>
        {% for property, color in colors %}
            <li>
                {{ property }} = {{ color }}
            </li>
        {% endfor %}
    </ul>
```

Note that Twig offers a key-value array loop just like PHP, in the form:

```html
    {% for <key>, <value> in <array> %}
```

Figure \ref{twig_blackwhite} shows a screenshot of our home page listing these Twig variables.

![Screenshot of home page listing Twig color array variable. \label{twig_blackwhite}](./03_figures/part05_sessions/6_black_white.png)

Now, let's add a second controller method, named `pinkblue()` that passes 2 different colours to our Twig template:

```php
    /**
     * @Route("/pinkblue", name="pinkblue")
     */
    public function pinkblue()
    {
        $colors = [
            'foreground' => 'blue',
            'background' => 'pink'
        ];

        $template = 'default/index.html.twig';
        $args = [
            'colors' => $colors,
        ];
        return $this->render($template, $args);
    }
```

Figure \ref{twig_colours} shows a screenshot of our second route, passing pink and blue colors to the Twig template.

![Screenshot of `/pinkblue` route passing different colours to Twig. \label{twig_colours}](./03_figures/part05_sessions/7_pink_blue.png)

## Working with sessions in Symfony Controller methods (project `session03`)

First, since we are going to be using sessions, let's return our default `index()` controller method to pass no arguments to the Twig template. This is because any color variables will be stored in the session and set by other controllers:

```php
    /**
     * @Route("/", name="default")
     */
    public function index()
    {
        $template = 'default/index.html.twig';
        $args = [
        ];
        return $this->render($template, $args);
    }
```

All we need to write to work with the current session object in a Symfony controller method is the following statement:

```
    $session = new Session();
```

Note, you also need to add the following `use` statement for the class using this code:

```php
    use Symfony\Component\HttpFoundation\Session\Session;
```

Note - do **not** use any of the standard PHP command for working with sessions. Do all your Symfony work through the Symfony session API. So, for example, do not use either of these PHP functions:

```php
    session_start(); // ----- do NOT use this in Symfony -------
    session_destroy(); // ----- do NOT use this in Symfony -------
```

You can now set/get values in the session by making reference to `$session`.

Note: You may wish to read about **how to start a session in Symfony**^[While a session will be started automatically if a session action takes places (if no session was already started), the Symfony documentation recommends your code starts a session if one is required. Here is the code to do so: `$session->start()`, but to be honest it's simpler to rely on Symfony to decide when to start a new session, since sometimes integrating this into your controller logic can be tricky (especially with controller redirects). You'll get errors if you try to start an already started session ...].


## Symfony's 2 session 'bags'

We've already met sessions - the Symfony 'flash bag', which stores messages in the session for one request cycle.

Symfony also offers a second kind of session storage, session 'attribute bags', which store values for longer, and offer a namespacing approach to accessing values in session arrays.

We store values in the attribute bag as follows using the `session->set()` method:

```php
    $session->set('<key>', <value>);
```

Here's how we store our colors array in the Symfony application session from our controllers:

```php
    // create colors array
    $colors = [
        'foreground' => 'blue',
        'background' => 'pink'
    ];

    // store colours in session 'colours'
    $session = new Session();
    $session->set('colors', $colors);
```

Note - also learn how to 'unset' values when you learn to set them. We can clear everything in a session by writing:

```php
    $session = new Session();
    $session->clear();
```


## Storing values in the session in a controller action

Let's refactor `DefaultController` method `pinkBlue()` which has route `/pinkblue` with logic to  store colours in the session and then re-direct Symfony to the home page route:

```php
    /**
     * @Route("/pinkblue", name="pinkblue")
     */
    public function pinkBlue()
    {
        // create colors array
        $colors = [
            'foreground' => 'blue',
            'background' => 'pink'
        ];

        // store colours in session 'colours'
        $session = new Session();
        $session->set('colors', $colors);

        return $this->redirectToRoute('default');
    }
```



If you add the Symfony Profiler (`composer req --dev profiler`) you can view session values in its session tab,, as show in Figure \ref{session_profiler}.


![Homepage with session colours applied via CSS. \label{session_profiler}](./03_figures/part05_sessions/8_profiler_session_variable.png)

Learn more at about Symfony sessions at:

- [Symfony and sessions](http://symfony.com/doc/current/components/http_foundation/sessions.html)

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
    /**
     * @Route("/default", name="default_colors")
     */
    public function defaultColors()
    {
        // store colours in session 'colours'
        $session = new Session();
        $session->clear();

        return $this->redirectToRoute('default');
    }
```



## Applying colours in HTML head `<style>` element (project `session04`)

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
```

So now we know we have our Twig variable `colours` assigned values (either from the session, or from the defaults. Now we can update the `<head>` of our HTML to include a new `body {}` CSS rule, that pastes in the values of our Twig array `colours['foreground']` and `colours['background']`:

```
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8" />
        <title>MGW - {% block pageTitle %}{% endblock %}</title>

        <style>
            @import '/css/flash.css';
            {% block stylesheets %}
            {% endblock %}

            body {
                color: {{ colours['foreground'] }};
                background-color: {{ colours['background'] }};
            }
        </style>
    </head>
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



