# Customising the display of generated forms

## First let's Bootstrap this project (project `form06`)

NOTE: The version of Boostrap is 5 - it may have been updated since this chapter was written.

Since the Twig Symfony component allows custom themes, of which Bootstrap is one of them, it is relatively easy to add Bootstrap to our website.

A great advantage of adding Bootstrap via a Twig theme is that components, such as the Form generation component, know about themes and will use them to decorate their output. So our form fields and buttons will make use of Bootstrap structures and CSS classes once we add this theme.

To add Bootstrap to a Symfony project we need to do 3 things:

1. Configure Twig to use the Bootstrap theme.

1. Add the Bootstrap CSS import into our base Twig template.

1. Add the Bootstrap JavaScript import into our base Twig template.

Learn more about the Bootstrap 5 theme on the Symfony documentation pages:

- [https://symfony.com/doc/current/form/bootstrap4.html](https://symfony.com/doc/current/form/bootstrap4.html)

## Configure Twig to use the Bootstrap theme

Well Symfony to generate forms using the Bootstrap theme by adding to `/config/packages/twig.yml`:

```twig
    form_themes: ['bootstrap_5_layout.html.twig']
``` 

So file `/config/packages/twig.yml` should now look as follows;

```yaml
    twig:
       default_path: '%kernel.project_dir%/templates'
       form_themes: ['bootstrap_5_layout.html.twig']
    when@test:
       twig:
          strict_variables: true

```

## Add the Bootstrap CSS and JS links into base Twig template

Visit the Bootstrap site:

- [https://getbootstrap.com/docs/5.0/getting-started/introduction/](https://getbootstrap.com/docs/5.0/getting-started/introduction/)

The Bootstrap QuickStart tells us to copy the JSDelivr CSS and JS `<link>` tags after the CSS `stylesheets` and  `javascripts` block of our `/templates/base.html.twig` Twig template. Add these `<link>` tags:

```twig
    <!DOCTYPE html>
    <html>
    <head>
        ...

       {% block stylesheets %}
       {% endblock %}

       {% block javascripts %}
       {% endblock %}

       <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css"
           rel="stylesheet" integrity="sha384-giJF6kkoqNQ00vy+HMDP7azOuL0xtbfIcaT9wjKHr8RbDVddVHyTfAAsrekwKmP1"
           crossorigin="anonymous" />

       <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"
           integrity="sha384-q2kxQ16AaE6UbzuKqyBE9/u/KzioAlnx2maXQHiDX9d4/zp8Ok3f+M7DPm+Ib6IU"
           crossorigin="anonymous"></script>
       <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.min.js"
           integrity="sha384-pQQkAEnwaBkjpqZ8RU1fF1AKtTcHJwFl3pblpTlHXybJjHpMYo79HY3hIi4NKxyj"
           crossorigin="anonymous"></script>
    </head>
```


## Run site and see some Bootstrap styling

Figure \ref{form_bootstrap} shows a screenshot how our new Student form looks now. We can see some basic Bootstrap styling with blue buttons, and sans-serif fonts etc. But the text boxes go right to the left/right edges of the browser window, with no padding etc.

![Basic Bootstrap styling of generated form. \label{form_bootstrap}](./03_figures/part03/20_bootstrap_form.png){ width=50% }

Figure \ref{form_bootstrap_source} shows the HTML source - we can see no page/content `<div>` elements around the form, which are needed as part of the guidelines of using Bootstrap.

![Basic HTML source of generated form. \label{form_bootstrap_source}](./03_figures/part03/21_bootstrap_form_source.png){ width=80% }


## Adding elements for navigation and page content

Let's ensure main `body` content of every page is inside a Bootstrap element.

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

![Form in nicely spaces HTML body. \label{form_nice_body}](./03_figures/part03/22_bootstrap_body.png){ width=50%}

## Add Bootstrap navigation bar

Let's add a title to our navigation bar, declaring this site `My Great Website`. This should be a link to the website root (we can just link to `#`).

Do the following:

1. Add some Bootstrap classes and a link around text `My Great Website !` in `base.html.twig`:

    ```html
           <nav class="navbar navbar-expand-lg navbar-dark bg-dark">

               <a class="navbar-brand me-auto" href="#">
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

            <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
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

![Navbar links aligned to the RIGHT for all website pages. \label{styled_links}](./03_figures/part03/24_styled_links.png)

## Adding the hamburger-menu and collapsible links

While it looks fine in the desktop, these links are lost with a narrow screen. Let's make them be replaced by a 'hamburger-menu' when the browser window is narrow.

We need to add a toggle drop-down button:

```html
    <button class="navbar-toggler bg-dark" data-bs-toggle="collapse"
        data-bs-target="#navbarNavDropdown">

        <span class="navbar-toggler-icon"></span>
    </button>
```

We also need to wrap a collapse `<div>` around our unordered list of links, with id of `navbarNavDropdown`, so that it's this list that is replaced by the hamburger-menu:

```html
    <div class="collapse navbar-collapse" id="navbarNavDropdown">
        <ul class="navbar-nav ms-auto mb-2">
            <li class="nav-item">...</li>
            <li class="nav-item">...</li>
        </ul>
    </div>
```

So our complete `<nav>` element now looks as follows:
```html
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand me-auto" href="#">
                My Great Website !
            </a>
            <button class="navbar-toggler bg-dark" data-bs-toggle="collapse"
                data-bs-target="#navbarNavDropdown">

                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNavDropdown">
                <ul class="navbar-nav ms-auto mb-2">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ path('student_list') }}">student actions</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="{{ url('student_new_form') }}">
                            Create NEW student
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
```


Figure \ref{hamburger} shows our simple black navbar from our base template.

![Animated hamburger links for narrow browser window. \label{hamburger}](./03_figures/part03/25_hamburger.png)

