
# Introduction to Symfony sessions

## Create a new project from scratch (project `sessions01`)

Let's start with a brand new web app project to learn about Symfony sessions:

```bash
    $ symfony new --webapp session01
```

## Remove encore entry points in base Twig template

If you are not using WebPack encore, remove the `encore_entry_` lines inside the `stylesheets` and `javascripts` blocks in the base template (`/templates/base.html.twig`). So these blocks will be empty in this parent template file:

```twig
    {% block stylesheets %}
    {% endblock %}

    {% block javascripts %}
    {% endblock %}
```

## Default controller - hello world

Create a new  controller `DefaultController` that renders a Twig template to say `Hello World` to us.

So the controller should look as this (you can speed things up using `make` and then editing the created file). If editing a generated controlle, don't forget to change the route pattern from `/default` to the website root of `\` in the annotation comment
:
```php
    namespace App\Controller;

    use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Component\Routing\Annotation\Route;

    class DefaultController extends AbstractController
    {
        #[Route('/', name: 'app_default')]
        public function index(): Response
        {
            $template = 'default/index.html.twig'; $args = [];
            return $this->render($template, $args);
        }
    }

```

Our home page default template `default/index.html.twig` can be this simple^[If we have a suitable HTML skeleton base template.]:

```twig
    {% extends 'base.html.twig' %}

    {% block title %} hello world {% endblock %}

    {% block body %}
        <p>
            hello world
        </p>
    {% endblock %}
```

## Twig foreground/background colours

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
    #[Route('/', name: 'app_default')]
    public function index(): Response
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


