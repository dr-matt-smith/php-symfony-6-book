

# DIY forms

## Preparation

This project assumes you are working with a copy of project `db03` - i.e. with a D.I.Y. controller and templates.

You can start with a copy from the book Github repositories if you wish:
- [https://github.com/dr-matt-smith/php-symfony-5-book-codes-databases-03-param-converter](https://github.com/dr-matt-smith/php-symfony-5-book-codes-databases-03-param-converter)

## Adding a form for new Student creation (project `form01`)

Let's create a DIY (Do-It-Yourself) HTML form to create a new student. We'll need:

- a controller method (and template) to display our new student form

    - route `/student/new`
    
        - with internal route name `student_new_form`

- a controller method to process the submitted form data

    - route `/student/processNewForm`
    
        - with internal route name `student_process_new_form`

The form will look as show in Figure \ref{new_student_form}.

![Form for a new student \label{new_student_form}](./03_figures/part03/1_new_student_form.png)

## Refacfor our `create(...)` method

Since we will now be creating new Students using a form, rather than passing the properties directly as `GET` partameters in thge URL, let's refactor our `create(...)` in class `StudentController` to be a private method (no route annotatiohn comment), that accepts 2 parameters and uses them to create a new Student obnject and store it in the database, then redirect to the list of students page:

```php
    private function create($firstName, $surname)
    {
        $student = new Student();
        $student->setFirstName($firstName);
        $student->setSurname($surname);
    
        $em = $this->getDoctrine()->getManager();
        $em->persist($student);
        $em->flush();
    
        return $this->redirectToRoute('student_list');
    }
```

## Twig new student form

Here is our new student form `/templates/student/new.html.twig':

```html
    {% extends 'base.html.twig' %}

    {% block pageTitle %}new student form{% endblock %}

    {% block body %}
        <h1>Create new student</h1>

        <form action="/student/processNewForm" method="POST">
                First Name:
                <input type="text" name="firstName">
            <p>
                Surname:
                <input type="text" name="surname">
            <p>
                <input type="submit" value="Create new student">
        </form>
    {% endblock %}
```

## Controller method (and annotation) to display new student form

Let's add a **new** action to `StudentController`.

NOTE: This should be **the FIRST** method in this class -  Since we don't want `/student/new` being treated as `/student/{id = 'new'}`, so our new form action method should be placed before our show action.  `<<<<<<<<`

Here is our `StudentController` method `newForm()` to display our Twig form:

```php
    /**
     * @Route("/student/new", name="student_new")
     */
    public function newForm()
    {
        $template = 'student/new.html.twig';
        $args = [
        ];
        return $this->render($template, $args);
    }

```



We'll also add a link to this form route in our list of students page. So we add to the end of `/templates/student/list.html.twig` the following link:

```html
        (... existing Twig code to show list of students here ...)

        <hr>
        <a href="{{ path('student_new_form')}}">
            create NEW student
        </a>
    {% endblock %}
```

## Controller method to process POST form data

We can access POST submitted data using the following expression:

```php
    $request->request->get(<POST_VAR_NAME>)
```

So we can extract and store in `$firstName` and `$surname` the POST `firstName` and `surname` parameters by writing the following:

```php
    $firstName = $request->request->get('firstName');
    $surname = $request->request->get('surname');
```

We will need access to the HTTP request, so we must declare a method parameter of `Request $request`. Symfony will now automatically provide this method with access to an object `$request`, which we can interrogate for things like the HTTP method of the request, and any name/value variables received in the request:

```php
    public function processNewFormAction(Request $request)
    {
```

Note: We have not **namespaced** class `Request`, so, at the top of our controller class declaration, we need to add an appropriate `use` statement, so PHP knows **which** `Request` class we are referring to. So we need to add the following before the class declaration:

```php
    use Symfony\Component\HttpFoundation\Request;
```

Our full listing for `StudentController` method `processNewForm()` looks as follows:
```php
    /**
     * @Route("/student/processNewForm", name="student_process_new_form")
     */
    public function processNewForm(Request $request)
    {
        // extract name values from POST data
        $firstName = $request->request->get('firstName');
        $surname = $request->request->get('surname');

        // forward this to the createAction() method
        return $this->create($firstName, $surname);
    }
```

NOTE: that we then invoke our existing `createAction(...)` method, passing on the extracted `$firsName` and `$surname` strings.

NOTE: This should be **the FIRST** method in this class (or at least before the **show** method )-  Since we don't want `/student/processNewForm` being treated as `/student/{id = 'new'}`, so our new form action method should be placed before our show action. If you get a Param Converter exception Student object not found you put this method **after** the show method...  `<<<<<<<<`



## Validating form data, and displaying temporary 'flash' messages in Twig

What should we do if an empty name string was submitted? We need to **validate** form data, and inform the user if there was a problem with their data.

Symfony offers a very useful feature called the 'flash bag'. Flash data exists for just 1 request and is then deleted from the session. So we can create an error message to be display (if present) by Twig, and we know some future request to display the form will no have that error message in the session any more.


## Three kinds of flash message: notice, warning and error

Typically we create 3 different kinds of flash notice:

- notice
- warning
- error

Our Twig template would style these differntly (e.g. pink background for errors etc.). Here is how to creater a flash message and have it stored (for 1 request) in the session:

```php
    $this->addFlash(
            'error',
            'Your changes were saved!'
        );
```

In Twig we can attempt to retrieve flash messages in the following way:

```html
    {% for flash_message in app.session.flashBag.get('notice') %}
        <div class="flash-notice">
            {{ flash_message }}
        </div>
    {% endfor %}
```


## Adding flash display (with CSS) to our Twig template (project `form02`)

First let's create a CSS stylesheet and ensure it is always loaded by adding its import into our `base.html.twig` template.

First create the directory `css` in `/public` - remember that `/public` is the Symfony public folder, where all public images, CSS, javascript and basic front controllers (`app.php` and `app_dev.php`) are served from).

Now create CSS file `/public/css/flash.css` containing the following:

```css
    .flash-error {
        padding: 1rem;
        margin: 1rem;
        background-color: pink;
    }
```

Next we need to edit our `/templates/base.html.twig` so that every page in our webapp will have imported this CSS stylesheet. Edit the `<head>` element in `base.html.twig` as follows:

```html
    <!DOCTYPE html>
    <html>
        <head>
            <meta charset="UTF-8" />
            <title>MGW - {% block pageTitle %}{% endblock %}</title>

            <style>
                @import '/css/flash.css';
            </style>
            {% block stylesheets %}{% endblock %}
        </head>
```



## Adding validation logic to our form processing controller method

Our form data is valid if **neither** name received was empty:

```php
    $isValid = !empty($firstName) && !empty($surname);
```

Now we can add the empty string test (and flash error message) to our `processNewFormn()` method as follows:

```php
    public function processNewForm(Request $request)
    {
        // extract name values from POST data
        $firstName = $request->request->get('firstName');
        $surname = $request->request->get('surname');

        // valid if neither value is EMPTY
        $isValid = !empty($firstName) && !empty($surname);
        if(!$isValid){
            $this->addFlash(
            'error',
            'student firstName/surname cannot be an empty string'
        );
            // forward this to the createAction() method
            return $this->newForm();
        }

        // forward this to the createAction() method
        return $this->create($firstName, $surname);
    }
```

So if the `$name` we extracted from the POST data is an empty string, then we add an `error` flash message into the session 'flash bag', and forward on processing of the request to our method to display the new student form again.

Finally, we need to add code in our new student form Twig template to display any error flash messages it finds. So we edit `/templates/student/new.html.twig` as follows:

```html
    {% extends '_base.html.twig' %}
    {% block pageTitle %}new student form{% endblock %}

    {% block body %}

        <h1>Create new student</h1>

        {% for flash_message in app.session.flashBag.get('error') %}
            <div class="flash-error">
                {{ flash_message }}
            </div>
        {% endfor %}

        (... show HTML form as before ...)
```

## Postback logic (project `form03`)

A common approach (and used in CRUD auto-generated code) is to combine the logic for displaying a form, and processing its submission, in a single method. The logic for this is that if any of the submitted data was invalid (or missing), then the default form processing can go back to re-displaying the form (with an appropriate 'flash' error message) to the user.

This approach is known as a 'postback' - i.e. that the submission of the form is POSTEd back to the same method that displayed the form.

The logic usually goes something like this:

1. Define a controller method route for both `GET` and `POST` HTTP methods

1. Attempt to find values in the `POST` request body

1. If the form was submitted by the `POST` method AND the data was all valid THEN

    - invoke the method to create the object/process the data and retuurn an appropriate success response

1. (else) If POST submitted but NOT valid THEN

    - create an appropriate flash error message in the session

1. return a response showing the form via Twig `render(...)` method

    - passing values, if we want a 'sticky' form remembering partly valid form values


Let's name our combined show form & process form controller method `newAction(...)`, name its internal route as `student_new`, and declare that only `POST` and `GET` HTTP requests are to be routed to this method^[By default a controller method that does not declare any specific HTTP methods will be used for **any** HTTP method matching the route pattern. So it is good practice to start limiting our controller methods to only those HTTP methods that are valid for how we wish our web application to behave...]:

```php

    /**
     * @Route("/student/new", name="student_new",  methods={"POST", "GET"})
     */
    public function newAction(Request $request)
    {
```

Remember, we will need access to the `Request` object to get access to the POST values, and to check with HTTP method the request was sent via.


The simplest request will be for the new student form to be displayed, the logic for that is from our old `newFormAction()`:

```php
    // render the form for the user
    $template = 'student/new.html.twig';
    $argsArray = [
    ];

    return $this->render($template, $argsArray);
```

The rest of the logic in this method will related to when the HTTP request is POST-submission of the form, and its validation. We can check whether the HTTP request was received as follows:

```php
    $isSubmitted = $request->isMethod('POST');
```

We can attempt to retrieve values from a POST submitted form as follows:

```php
    // attempt to find values in POST variables
    $firstName = $request->request->get('firstName');
    $surname = $request->request->get('surname');
```

Note: If there was no named variable in the POST data, the variables `$firstName` and `$surnamne` will return `null` (and so will register as `true` when tested with `isEmpty(...)`).


If our form validation logic is simply that neither name can be an empty string (or null), then we can write an expression to check that neither is empty as follows:

```php
    $isValid = !empty($firstName) && !empty($surname);
```

Our core logic for this controller is that **if** the request was an HTTP `POST` method **and** the values received were value, then we are happy to accept the form data and go off an create a new object (and return an appropriate response). We can write this as follows:

```php
    // if SUBMITTED & VALID - go ahead and create new object
    if ($isSubmitted && $isValid) {
        return $this->createAction($firstName, $surname);
    }
```

NOTE: Since our method is invoking a `return`, then no further processing of statements in the method will occur. I.e. we can locate our logic for (re)displaying the form after this `if`-test.

If it was a `POST` submitted form but the data was **not** valid, then we should create a 'flash' error message in the session:

```php
   if ($isSubmitted && !$isValid) {
        $this->addFlash(
            'error',
            'student firstName/surname cannot be an empty string'
        );
    }
```

We can now simply replace the previous 2 methods `processNewFormAction()` and `newFormAction()` with our new single postback method `new(...)` as follows:

```php
    /**
     * @Route("/student/new", name="student_new_form", methods={"POST", "GET"})
     */
    public function new(Request $request) {
        // attempt to find values in POST variables
        $firstName = $request->request->get('firstName');
        $surname = $request->request->get('surname');

        // valid if neither value is EMPTY
        $isValid = !empty($firstName) && !empty($surname);

        // was form submitted with POST method?
        $isSubmitted = $request->isMethod('POST');

        // if SUBMITTED & VALID - go ahead and create new object
        if ($isSubmitted && $isValid) {
            return $this->create($firstName, $surname);
        }

        if ($isSubmitted && !$isValid) { $this->addFlash(
            'error',
            'student firstName/surname cannot be an empty string'
        );
        }
        // render the form for the user
        $template = 'student/new.html.twig';
        $args = [
            'firstName' => $firstName,
            'surname' => $surname
        ];
        return $this->render($template, $args);
    }
```


Finally (!) we can achieve a 'sticky' form by passing any value in `$firstName` and `$surname` to our Twig template in its argument array:

```php
    $argsArray = [
        'firstName' => $firstName,
        'surname' => $surname
    ];
```

These will either be null, or have the string values from the POST submitted form attempt. We re-display these values (if no null) by adding `value=""` attributed in our Twig form template `/templates/student/new.html.twig` as follows:

```twig
    <form action="/student/new" method="POST">
            First Name:
            <input type="text" name="firstName" value={{ firstname }}>
        <p>
            Surname:
            <input type="text" name="surname" value={{ surname }}>
        <p>
            <input type="submit" value="Create new student">
    </form>
```


NOTE: We have **changed** the form action to `"/student/new"`, so that the form POST submission will be routed to the same method (`new()`) as the one to display the form.


## Extra notes

Here is how to work with Enum style drop-down combo-boxes:

- [Articled on Symfony Enums in forms frmo Maxence POUTORD](http://www.maxpou.fr/dealing-with-enum-symfony-doctrine/)
