# Customising the display of generated forms

## First let's Bootstrap this project (project `form06`)

Since the Twig Symfony component allows custom themes, of which Bootstrap 4 is one of them, it is relatively easy to add Bootstrap to our website.

A great advantage of adding Bootstrap via a Twig theme is that components, such as the Form generation component, know about themes and will use them to decorate their output. So our form fields and buttons will make use of Bootstrap structures and CSS classes once we add this theme.

To add Bootstrap to a Symfony project we need to do 3 things:

1. Configure Twig to use the Bootstrap theme.

1. Add the Bootstrap CSS import into our base Twig template.

1. Add the Bootstrap JavaScript import into our base Twig template.

Learn more about the Bootstrap 4 theme on the Symfony documentation pages:

- [https://symfony.com/doc/current/form/bootstrap4.html](https://symfony.com/doc/current/form/bootstrap4.html)

## Configure Twig to use the Bootstrap theme

Well Symfony to generate forms using the Bootstrap theme by adding:
 
```twig
    form_themes: ['bootstrap_4_layout.html.twig']
``` 

to `/config/packages/twig.yml` file. So this file should now look as follows;

```yaml
    twig:
        paths: ['%kernel.project_dir%/templates']
        debug: '%kernel.debug%'
        strict_variables: '%kernel.debug%'
        form_themes: ['bootstrap_4_layout.html.twig']
```

## Add the Bootstrap CSS import into our base Twig template

The Bootstrap QuickStart tells us to copy the CSS `<link>` tag from here:

- [https://getbootstrap.com/docs/4.4/getting-started/introduction/#css](https://getbootstrap.com/docs/4.4/getting-started/introduction/#css)


into the CSS part of our `/templates/base.html.twig` Twig template. Add this `<link>` tag just before the `stylesheets` block:

```twig
    <!DOCTYPE html> <html>
    <head>
        <meta charset="UTF-8" />
        <title>MGW - {% block pageTitle %}{% endblock %}</title>
        <style>
            @import '/css/flash.css';
        </style>

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
        
        {% block stylesheets %}{% endblock %}
        
    </head>
   ...

```


## Add the Bootstrap JavaScript import into our base Twig template.

The Bootstrap QuickStart tells us to copy the JS `<script>` tags from here:

- [https://getbootstrap.com/docs/4.4/getting-started/introduction/#js](https://getbootstrap.com/docs/4.4/getting-started/introduction/#js)

into the last part of the `<body>` element in `/templates/base.html.twig` Twig template. Add these `<script>` tags just after the `javascripts` block:

```twig
    ...
    
    <body>
    <nav>
        <ul>
            <li>
                <a href="{{ path('student_list') }}">student actions</a>
            </li>
        </ul>
    </nav>
    
        {% block body %}{% endblock %}
    
        {% block javascripts %}{% endblock %}
    
<script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>

    
    </body>
</html>

```

## Run site and see some Bootstrap styling

Figure \ref{form_bootstrap} shows a screenshot how our new Student form looks now. We can see some basic Bootstrap styling with blue buttons, and sans-serif fonts etc. But the text boxes go right to the left/right edges of the browser window, with no padding etc.

![Basic Bootstrap styling of generated form. \label{form_bootstrap}](./03_figures/part03/20_bootstrap_form.png)

Figure \ref{form_bootstrap_source} shows the HTML source - we can see no page/content `<div>` elements around the form, which are needed as part of the guidelines of using Bootstrap.

![Basic HTML source of generated form. \label{form_bootstrap_source}](./03_figures/part03/21_bootstrap_form_source.png)


## Adding elements for navigation and page content

Let's ensure main `body` content of every page is inside a Bootstrap 4 element.

We need to wrap a Bootstrap container and row divs around the `body` Twig block.

Replace the existing `body` block in template `base.html.twig` with the following:

```twig

    <div class="container">
        <div class="row">
            <div class="col-sm-12">

            {% block body %}{% endblock %}

            </div>
        </div>
    </div>
```

When we visit the site not, as we can see in Figure \ref{form_nice_body}, the page content is within a nicely styled Bootstrap container, with associated margins and padding.

![Basic HTML source of generated form. \label{form_nice_body}](./03_figures/part03/22_bootstrap_body.png)

## Add Bootstrap navigation bar

Let's add a title to our navigation bar, declaring this site `My Great Website`. This should be a link to the website root (we can just link to `#`).

Do the following:

1. Add a new CSS stylesheet to make our navbar background BLACK. Create file `/public/css/nav.css` containing:

    ```css
       nav {
           background-color: black;
       }
    ```

1. Add an `@import` statement for this stylesheet in the `<style>` element in our `base.html.twig` master template:

    ```html
       <!DOCTYPE html> <html>
       <head>
           <meta charset="UTF-8" />
           <title>MGW - {% block pageTitle %}{% endblock %}</title>
           <style>
               @import '/css/flash.css';
               @import '/css/nav.css';
           </style>
        
           ...
    ```
    
1. Add some Bootstrap classes and a link around text `My Great Website !` in `base.html.twig`:

    ```html
           <nav class="navbar navbar-expand-lg navbar-dark navbar-bg mb-5">
               <a style="margin-left: 0.1rem;" class="navbar-brand space-brand" href="#">
                   My Great Website !
               </a>
       
               <ul>
                   <li>
                       <a href="{{ path('student_list') }}">student actions</a>
                   </li>
               </ul>
           </nav>
    ```



Figure \ref{black_nav} shows our simple black navbar from our base template.

![Black navbar for all website pages. \label{black_nav}](./03_figures/part03/23_title.png)

## Styling list of links in navbar

Let's now have links for list of students and creating a NEW student, properly styled by our Bootstrap theme.

We need to add a Bootstrap styled unordered-list in the `<nav>` element, with links to routes `student_list` and `student_new`:

```html
        <nav class="navbar navbar-expand-lg navbar-dark navbar-bg mb-5">
            <a style="margin-left: 0.1rem;" class="navbar-brand space-brand" href="#">
                My Great Website !
            </a>
    
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link" href="{{ url('student_list') }}">
                        student list
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ url('student_new_form') }}">
                        Create NEW student
                    </a>
                </li>
            </ul>
     
        </nav>
```


Figure \ref{styled_links} shows the navbar with our 2 styled links.

![Navbar links for all website pages. \label{styled_links}](./03_figures/part03/24_styled_links.png)

## Adding the hamburger-menu and collapsible links

While it looks fine in the desktop, these links are lost with a narrow screen. Let's make them be replaced by a 'hamburger-menu' when the browser window is narrow.

We need to add a toggle drop-down button:

```html
    <button class="navbar-toggler" type="button" data-toggle="collapse"
            data-target="#navbarNavDropdown" aria-controls="navbarNavDropdown"
            aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
```

We also need to wrap a collapse `<div>` around our unordered list of links, with id of `navbarNavDropdown`, so that it's this list that is replaced by the hamburger-menu:

```html
    <div class="collapse navbar-collapse" id="navbarNavDropdown">
        <ul class="navbar-nav ml-auto">
            <li>...</li>
            <li>...</li>
        </ul>
    </div>
```

So our complete `<nav>` element now looks as follows:
```html
    <nav class="navbar navbar-expand-lg navbar-dark navbar-bg mb-5">
        <a style="margin-left: 0.1rem;" class="navbar-brand space-brand" href="#">
            My Great Website !
        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNavDropdown">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link" href="{{ url('student_list') }}"> student list
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ url('student_new_form') }}">
                        Create NEW student
                    </a>
                </li>
            </ul>
        </div>
    </nav>
```


Figure \ref{black_nav} shows our simple black navbar from our base template.

![Animated hamburger links for narrow browser window. \label{hamburger}](./03_figures/part03/25_hamburger.png)

