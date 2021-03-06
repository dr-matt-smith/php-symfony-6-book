# Customizing display of Symfony forms

## Understanding the 3 parts of a form  (project `form07`)

In a controller we  create a `$form` object, and pass this as a Twig variable to the template `form`.
Twig renders the form in 3 parts:

- the opening `<form>` tag
- the sequence of form fields (with labels, errors and input elements)
- the closing `</form>` tag

This can all be done in one go (using Symfony/Twig defaults) with the Twig `form()` function, or we can use Twigs  3 form functions for rendering (displaying) each part of a form, these are:

- `form_start()`
- `form_widget()`
- `form_end()`


So we could write the `body` block of our Twig template (`/app/Resources/views/students/new.html.twig`) for the new `Student` form to the following:

```html
    {% block body %}
        <h1>Create new student</h1>
        {{ form_start(form) }}
        {{ form_widget(form) }}
        {{ form_end(form) }}
    {% endblock %}
```

Although since we're not adding anything between these 3 Twig functions' output, the result will be the same form as before.

## Using a Twig form-theme template

In addition to the Bootstrap layouts, Symfony provides several other useful Twig templates for common form layouts.

These include:

- wrapping each form field in a `<div>`
    - form_div_layout.html.twig

- put form inside a table, and each field inside a table row `<tr>` element
    - form_table_layout.html.twig



For example, to use the `div` layout we can declare this template be used for all forms in the `/config/packages/twig.yml` file as follows:

```yaml
    twig:
        form_themes: ['form_div_layout.html.twig']
```



## DIY (Do-It-Yourself) form display customisations

Each form field can be rendered all in one go in the following way:

```
    {{ form_row(form.<FIELD_NAME>) }}
```

For example, if the form has a field `name`:

```
    {{ form_row(form.name) }}
```

So we could display our new student form this way:

```
    {% block body %}
        <h1>Create new student</h1>
        {{ form_start(form) }}

        {{ form_row(form.firstName) }}
        {{ form_row(form.surname) }}
        {{ form_row(form.save) }}

        {{ form_end(form) }}
    {% endblock %}
```

## Customising display of parts of each form field

Alternatively, each form field can have its 3 constituent parts rendered separately:

- label (the text label seen by the user)
- errors (any validation error messages)
- widget (the form input element itself)

For example:

```
   <div class="form-group">
        <div class="errors">
        {{ form_errors(form.firstName) }}
        </div>

        {{ form_label(form.firstName) }}

        {{ form_widget(form.firstName) }}
    </div>
```


The above would output the following HTML for the `firstName` property (if the errors list was empty):

```html

   <div class="form-group">
    <div class="errors">
        
    </div>

    <label for="student_firstName" class="required">First name</label>

    <input type="text" id="student_firstName" name="student[firstName]" required="required" class="form-control" />
    </div>
```

### Making custom form div displays explicitly with colour and borders

Let's show how we are controlling each part of the form layout by making each `div` spaced from each other, with a thin border.

We can do this by declaring a `stylesheets` block for this page's CSS, and adding class `border1` to each div.

Here is the CSS Twig block:

```twig
    {% block stylesheets %}
        <style>
        .border1 {
            border: black solid 1px;
            padding: 5px;
            margin: 10px;
            background-color: lightblue;
        }
        </style>
    {% endblock %}
```

Here is the HTML body block with CSS class `border1` to each div.

```twig
    {% block body %}
        <h1>Create new student</h1>
        {{ form_start(form) }}

        <div class="form-group border1">
            <div class="errors">
                {{ form_errors(form.firstName) }}
            </div>

            {{ form_label(form.firstName) }}

            {{ form_widget(form.firstName) }}
        </div>

        <div class="form-group border1">
            <div class="errors">
                {{ form_errors(form.surname) }}
            </div>

            {{ form_label(form.surname) }}

            {{ form_widget(form.surname) }}
        </div>

        <div class="form-group border1">
            {{ form_row(form.save) }}
        </div>

        {{ form_end(form) }}
    {% endblock %}
```



Figure \ref{blue_borders} shows how we have been able to separate out each part of the form in out Twig template.

![Screenshot showing styled HTML divs for each part of the form. \label{blue_borders}](./03_figures/part03/31_custom_form_display.png)




Learn more at:

- [The Symfony form customisation page](https://symfony.com/doc/current/form/form_customization.html)


## Specifying a form's **method** and **action**

While Symfony forms default to `POST` submission and a postback to the same URL, it is possible to specify the method and action of a form created with Symfony's form builder. For example:

```php
    $formBuilder = $formFactory->createBuilder(FormType::class, null, array(
        'action' => '/search',
        'method' => 'GET',
    ));
```



Learn more at:

- [Introduction to the Form component](https://symfony.com/doc/current/components/form.html)

