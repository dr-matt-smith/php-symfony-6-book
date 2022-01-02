

# Using the Bootstrap responsive Twig theme \label{appendix_bootstrap}

## Twig setting in `congig.yml` for stating default form theme

Symfony now lets you specify Twitter Bootstrap for all your forms with these lines in `/app/config/config.yml`:

Either use this:
```
    twig:
        form_theme: ['bootstrap_3_layout.html.twig']
```

Or this (if you wish to add more themes the list later:
```
    twig:
        form_themes:
          - 'bootstrap_3_layout.html.twig'
```

That's it! Now all  your forms should automatically be formatted with boostrap DIVs and CSS classes.

Source:

- [Original Symfony blog October 2016](http://symfony.com/blog/new-in-symfony-2-6-bootstrap-form-theme)
- [details of new config format ](https://stackoverflow.com/questions/38012156/bootstrap-form-theme-in-symfony-3-1)


## HTML code to import JS and CS from `/web`

```
    http://symfony.com/doc/current/best_practices/web-assets.html
```

Source:

- [](http://symfony.com/doc/current/best_practices/web-assets.html)

## Twig and Bootstrap

[page recommends composer Boostrap import](https://stackoverflow.com/questions/36453039/what-is-the-correct-way-to-add-bootstrap-to-a-symfony-app)

Install Bootstrap and JQuery with Composer:

```
    composer require twbs/bootstrap
    composer require components/jquery
```

In `config.yml` set up your `assetic` assets management:

```json
    assetic:
        debug:          "%kernel.debug%"
        use_controller: false
        bundles:        [ '*Place your bundle names here*' ]

        filters:
            cssrewrite: ~
        assets:
            bootstrap_js:
                inputs:
                    - %kernel.root_dir%/../vendor/twbs/bootstrap/dist/js/bootstrap.js
            bootstrap_css:
                inputs:
                    - %kernel.root_dir%/../vendor/twbs/bootstrap/dist/css/bootstrap.css
                    - %kernel.root_dir%/../vendor/twbs/bootstrap/dist/css/bootstrap-theme.css
                filters: [cssrewrite]

            bootstrap_glyphicons_ttf:
                inputs:
                    - %kernel.root_dir%/../vendor/twbs/bootstrap/dist/fonts/glyphicons-halflings-regular.ttf
                output: "fonts/glyphicons-halflings-regular.ttf"
            bootstrap_glyphicons_eot:
                inputs:
                    - %kernel.root_dir%/../vendor/twbs/bootstrap/dist/fonts/glyphicons-halflings-regular.eot
                output: "fonts/glyphicons-halflings-regular.eot"
            bootstrap_glyphicons_svg:
                inputs:
                    - %kernel.root_dir%/../vendor/twbs/bootstrap/dist/fonts/glyphicons-halflings-regular.svg
                output: "fonts/glyphicons-halflings-regular.svg"
            bootstrap_glyphicons_woff:
                inputs:
                    - %kernel.root_dir%/../vendor/twbs/bootstrap/dist/fonts/glyphicons-halflings-regular.woff
                output: "fonts/glyphicons-halflings-regular.woff"

            jquery:
                inputs:
                    - %kernel.root_dir%/../vendor/components/jquery/jquery.js
```

The in your Twig `base` template ensure every page loads the CSS and JS for Bootstrap and JQuery via Asset4ic:

```
    <head>
        ...
        {% stylesheets '@bootstrap_css' %}
            <link rel="stylesheet" type="text/css" media="screen" href="{{ asset_url }}"/>
        {% endstylesheets %}
    </head>
    <body>

        ....

        {% javascripts '@jquery' '@bootstrap_js' %}
            <script type="text/javascript" src="{{ asset_url }}"></script>
        {% endjavascripts %}
    </body>
```


## Installing Assetic Bundle

You need to install the Assetic Bundle for Symfony 3:


```bash
    composer require symfony/assetic-bundle
```

And you need to **enable** the bundle in the Kernal (`/app/AppKernel.php`):

```php
    // app/AppKernel.php

    // ...
    class AppKernel extends Kernel
    {
        // ...

        public function registerBundles()
        {
            $bundles = array(
                // ...
                new Symfony\Bundle\AsseticBundle\AsseticBundle(),
            );

            // ...
        }
    }
```

Sources:

- [Symfony documentation](http://symfony.com/doc/current/assetic/asset_management.html)
- [Boostrap in Symfony article](https://coderwall.com/p/cx1ztw/bootstrap-3-in-symfony-2-with-composer-and-no-extra-bundles)
