
# Automatic forms generated from Entities

## Using the Symfony form generator (project `form04`)

Given an object of an Entity class, Symfony can analyse its property names and types, and generate a form (with a little help). That's what we'll do in this chapter.

However, first, let's simplify something for later, we'll make our `createAction()` expect to be given a reference to a `Student` object (rather than expect 2 string parameters `firstName` and `surname`):

```php
    public function createAction($student)
    {
        $em = $this->getDoctrine()->getManager();
        $em->persist($student);
        $em->flush();

        return $this->listAction($student->getId());
    }
```

## The form generator


So in a controller we can create a `$form` object, and pass this as a Twig variable to the template `form`. Twig offers 4 special functions for rendering (displaying) forms, these are:

- `form()` :: display the whole form (i.e. display the whole thing in one line!)
- `form_start()` :: display the beginning of the form
- `form_widget()` :: display all the fields etc.
- `form_end()` :: display the end of the form


So we can simplify the `body` block of our Twig template (`/app/Resources/views/students/new.html.twig`) for the new `Student` form to the following:

```html
    {% block body %}
        <h1>Create new student</h1>
        {{ form(form) }}
    {% endblock %}
```

That's it! No `<form>` element, no `<input>`s, no submit button, no labels! Even flash messages (relating to form validation errors) will be displayed by this function Twig function (global form errors at the top, and field specific errors by each form field).

The 'magic' happens in the controller method...



## Updating `StudentController->newFormAction()`

First, our controller method will need to pass a Twig variable `form` to the `render()` method. This will be created for us by the `createView()` method of a Symfony form object. So `newAction()` will end as follows:

```php
    $argsArray = [
        'form' => $form->createView(),
    ];

    $templateName = 'students/new';
    return $this->render($templateName . '.html.twig', $argsArray);
```

Our method will use Symfony's FormBuilder to create the form for us, based on an instance of class `Student`. First we create a new, empty `Student` object, and then use Symfony's `createFormBuilder()` method to create a form based on the Entity class of our `$student` object:


```php
    public function newFormAction(Request $request)
    {
        // create a new Student object
        $student = new Student();

        // create a form with 'firstName' and 'surname' text fields
        $form = $this->createFormBuilder($student)
            ->add('firstName', TextType::class)
            ->add('surname', TextType::class)
            ->add('save', SubmitType::class, array('label' => 'Create Student'))->getForm();
```

Note - for the above code to work we also need to add two `use` statements so that PHP knows about the classes `TextType` and `SubmitType`. These can be found in the form extension Symfony component:

```php
    use Symfony\Component\Form\Extension\Core\Type\TextType;
    use Symfony\Component\Form\Extension\Core\Type\SubmitType;
```

We ask Symfony to 'handle' the request for us. If the HTTP request was a POST submission, then the submitted values will be used to populate our `$student` object. Otherwise, if GET method, the form will be an empty, new form.

```php
    // if was POST submission, extract data and put into '$student'
    $form->handleRequest($request);
```

Forms have basic validation. The default for text entity properties is `NOT NULL`, so both name fields will be validated this way - both through HTML 5 validation and on the server side. If the form was submitted (via POST) and is valid, then we'll go ahead and create a new `Student` object as before:

```php
    // if SUBMITTED & VALID - go ahead and create new object
    if ($form->isSubmitted() && $form->isValid()) {
        return $this->createAction($student);
    }
```

If not submitted (or not valid), then the logic falls through to displaying the form via Twig. The full listing for our improved `newAction()` method is as follows:

```php
    /**
     * @Route("/student/new", name="student_new",  methods={"POST", "GET"})
     */
    public function newAction(Request $request)
    {
        // create a task and give it some dummy data for this example
        $student = new Student();

        // create a form with 'firstName' and 'surname' text fields
        $form = $this->createFormBuilder($student)
            ->add('firstName', TextType::class)
            ->add('surname', TextType::class)
            ->add('save', SubmitType::class, array('label' => 'Create Student'))->getForm();

        // if was POST submission, extract data and put into '$student'
        $form->handleRequest($request);

        // if SUBMITTED & VALID - go ahead and create new object
        if ($form->isSubmitted() && $form->isValid()) {
            return $this->createAction($student);
        }

        // render the form for the user
        $template = 'student/new.html.twig';
        $argsArray = [
            'form' => $form->createView(),
        ];

        return $this->render($template, $argsArray);
    }
```

We can see that the method does the following:

1. creates a new (empty) `Student` records `$students
1. creates a new form builder, passing in `$student`, and stating that we want it to create a HTML form input element for the `name` field, and also a submit button (`SubmitType`) with the label `Create Student`. We chain these method calls in sequence, making use of the form builder's 'fluent' interface, and store the created form object in PHP variable `$form`.
1. Finally, we create a Twig argument array, passing in the form object `$form` with Twig variable name `form`, and tell Twig to render the template `student/new.html.twig`.

Figure \ref{form_builder_student_form} shows a screenshot of the resulting form.

![Symfony generated new student form. \label{form_builder_student_form}](./03_figures/part03/2_generated_new_form.png)

## Postback - form submits to same URL

If we look at the HTML in the source of our web page (see Figure \ref{form_html}), we can see that the form has no `action` attribute, which means that when POST submitted, it will be submitted to the same URL (i.e. a our `newAction()`). However, since we've already written our logic to process a **post-back** like this, then our code will work :-)

![HTML source of generated form \label{form_html}](./03_figures/part03/3_html.png)


## Entering data and submitting the form

We find, however, that we haven't done enough if we actually enter a name (e.g. `joe-smith`) and submit the form via the submit button. Figure \ref{form_builder_post} shows that we just see a new empty form again! What we expect when we click a form submit button is for the entered values to be submitted to the server as an HTTP POST method. This is what has happned, **but** this request has been sent to the same URL as we used to display the form, i.e. `localhost:8000/students/new`. At present, our controller method does not distinguish between `GET` and `POST` methods, so simply responds by rendering the form again base on, another, new empty `Student` object. The Symfony footer profile bar shows us that it was a `POST` HTTP method request by writing `POST@students_new_form` (the name of the matched route, as defined in the controller annotation comment).

![Form re-displayed depsite `POST` submission of name `joe-smith`. \label{form_builder_post}](./03_figures/forms/3_post_method_sm.png)

We can see **why** the form submits to the same request URL as was used to display the form, if we look at the generated HTML (Chrome right-click `View Page Source`):

```html
        <h1>Create new student</h1>

        <form name="form" method="post">
        <div id="form"><div><label for="form_name" class="required">Name</label>
        <input type="text" id="form_name" name="form[name]" required="required" /></div>
        <div>
        <button type="submit" id="form_save" name="form[save]">Create Student</button></div>
        <input type="hidden" id="form__token" name="form[_token]" value="TJM9iQSmrWWdYLVcbflJl5-iaEbYUtVXQiBN0ctqUI8" /></div>
        </form>
```

Becase there is no `action` attribute in the `<form>` tag, then browsers automatically submit back to the same URL. This is known in web development as a **postback** and is very common^[read more at the [Wikipedia postback page](https://en.wikipedia.org/wiki/Postback)].



If we use the Chrome developer tools again, after submitting name `joe-smith` we can see that the name has been sent in the body of the `POST` request to our webapp, as `form[name]`. We can see these details in Figure \ref{form_builder_post_chrome}.

![Chrome developer tools showing `POST` submitted variable `joe-smith`. \label{form_builder_post_chrome}](./03_figures/forms/5_post_method_chome_sm.png)

We can also delve further into the details of the request and our Symfony applications handing of the request by clicking on the Symfony debug toolbar, and, for example, clicking the `Request` navigation link on the left. Figure \ref{form_builder_post_profiler} shows us the `POST` variables received.

![Chrome developer tools showing `POST` submitted variable `joe-smith`. \label{form_builder_post_profiler}](./03_figures/forms/4_post_profiler_sm.png)

## Detecting and processing postback form submission (and validation) (`project08`)

Since the form is posted back to the same URL as to display the form, then the same controller will be invoked. So we need to add some conditional logic in our controller to decide what to do. This logic will look like this:

```
    prepare the form
    tell the form to handle the request (i.e. get data from the Request into the form if its a postback)

    IF form has been submitted (POST method) AND values submitted are all valid THEN
        process the form data appropriately
        return an approprate Response (or redirect appropriately)

    OTHERWISE
        return a Response that renders the form
```

First let's do something really simply, if we detect the form has been submitted, let's just `var_dump()` the name received in the request and `die()`.

```php
   public function newFormAction(Request $request)
    {
        // create a task and give it some dummy data for this example
        $student = new Student();

        $form = $this->createFormBuilder($student)
            ->add('name', TextType::class)
            ->add('save', SubmitType::class, array('label' => 'Create Student'))
            ->getForm();

        /// ---- start processing POST submission of form
        $form->handleRequest($request);

        if($form->isSubmitted()){
            $student = $form->getData();
            $name = $student->getName();

            print "name received from form is '$name'";
            die();
        }

        $argsArray = [
            'form' => $form->createView(),
        ];

        $templateName = 'students/new';
        return $this->render($templateName . '.html.twig', $argsArray);
    }
```

So as we can see above, after creating the form, we tell the form to examine the HTTP request to determine if it was a postback (i.e. POST method), and if so, to extract data from the request and store that data in the `Student` object inside the form:

```php
    $form->handleRequest($request);
```

Next, we can now test (with form method `isSubmitted()`) whether this was a POST request, and if so, we'll extract the `Student` object into `$student`, then get the name from this object, into `$name`, then print out the name and `die()`:

```php
    if($form->isSubmitted()){
        $student = $form->getData();
        $name = $student->getName();

        print "name received from form is '$name'";
        die();
    }
```

However, if the form was not a postback submission (i.e. `isSubmitted()`), then we continue to create our Twig argument array and render the template to show the form.

The output we get, when submitting the name `joe-smith` with the above is shown in Figure \ref{form_post_confirmation}.

![Confirmation of postback received name`joe-smith`. \label{form_post_confirmation}](./03_figures/forms/6_postback_confirmatio_sm.png)

## Invoking the `createAction(...)` method when valid form data submitted

Let's write code to submit the extracted name property of the `Student` object in the form, to our existing `createAction(...)` method. So our conditional block, for the condition that if the form has been subnmitted **and** its data is valid will be:

```php
    if ($form->isSubmitted() && $form->isValid()) {
        $student = $form->getData();
        $name = $student->getName();
        return $this->createAction($name);
    }
```

Here is a reminder of our `createAction($name)` method. Note that the final statement has been to redirect to the list of students route, after successful creation (and database persistance) of a new student object:

```php
    public function createAction($name)
    {
        $student = new Student();
        $student->setName($name);

        // entity manager
        $em = $this->getDoctrine()->getManager();

        // tells Doctrine you want to (eventually) save the Student (no queries yet)
        $em->persist($student);

        // actually executes the queries (i.e. the INSERT query)
        $em->flush();

        return $this->redirectToRoute('students_list');
    }
```

## Final improvements (`project09`)

The final changes we might make include:

- to **remove** the route annotation for method `createAction(...)` - so it can only be invoked through our postback new student form route
- refactor  method `createAction(...)` to receive a `Student` object - simplyfying the code in each method

So the refactored listing for method `createAction(...)` is:

```php
    /**
     * @param Student $student
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function createAction(Student $student)
    {
        // entity manager
        $em = $this->getDoctrine()->getManager();

        // tells Doctrine you want to (eventually) save the Student (no queries yet)
        $em->persist($student);

        // actually executes the queries (i.e. the INSERT query)
        $em->flush();

        return $this->redirectToRoute('students_list');
    }
```

And our refactored method `newFormAction()` is:

```php
   public function newFormAction(Request $request)
    {
        // create a task and give it some dummy data for this example
        $student = new Student();

        $form = $this->createFormBuilder($student)
            ->add('name', TextType::class)
            ->add('save', SubmitType::class, array('label' => 'Create Student'))
            ->getForm();


        /// ---- start processing POST submission of form
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $student = $form->getData();
            return $this->createAction($student);
        }

        $argsArray = [
            'form' => $form->createView(),
        ];

        $templateName = 'students/new';
        return $this->render($templateName . '.html.twig', $argsArray);
    }
```

## Video tutorials about Symfony forms

Here are some video resources on this topic:

- [Code Review form validation with `@Assert`](https://codereviewvideos.com/course/beginner-s-guide-to-symfony-3-forms/video/validating-form-data-with-symfony-3)

