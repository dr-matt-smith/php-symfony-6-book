
# Automatic forms generated from Entities

## Using the Symfony form generator (project `form04`)

Given an object of an Entity class, Symfony can analyse its property names and types, and generate a form (with a little help). That's what we'll do in this chapter.

However, first, let's simplify something for later, we'll make our `createAction()` expect to be given a reference to a `Student` object (rather than expect 2 string parameters `firstName` and `surname`):

```php
    public function createAction(Student $student)
    {
        $em = $this->getDoctrine()->getManager();
        $em->persist($student);
        $em->flush();

        return $this->redirectToRoute('student_list');
    }
```

## The Symfony form generator via Twig

In a controller we can create a `$form` object, and pass this as a Twig variable to the template `form`. Twig offers 4 special functions for rendering (displaying) forms, these are:

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



## Updating `StudentController->new()`

First, our controller method will need to pass a Twig variable `form` to the `render()` method. This will be created for us by the `createView()` method of a Symfony form object. So `new()` will end as follows:

```php
    $argsArray = [
        'form' => $form->createView(),
    ];

    $templateName = 'students/new';
    return $this->render($templateName . '.html.twig', $argsArray);
```

Our method will use Symfony's FormBuilder to create the form for us, based on an instance of class `Student`. First we create a new, empty `Student` object, and then use Symfony's `createFormBuilder()` method to create a form based on the Entity class of our `$student` object:


```php
    public function new(Request $request)
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

If not submitted (or not valid), then the logic falls through to displaying the form via Twig. The full listing for our improved `new()` method is as follows:

```php
    /**
     * @Route("/student/new", name="student_new",  methods={"POST", "GET"})
     */
    public function new(Request $request)
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

If we look at the HTML in the source of our web page (see Figure \ref{form_html}), we can see that the form has no `action` attribute, which means that when POST submitted, it will be submitted to the same URL (i.e. a our method  `new()`). However, since we've already written our logic to process a **post-back** like this, then our code will work :-)

![HTML source of generated form \label{form_html}](./03_figures/part03/3_html.png)

## Using form classes (project `form05`)

Although simple forms can be created inside a controller method as above, it's good practice to
create a separate from 'type' class to create each form.

Rather than write one from scratch, some of the work can be done for us using the **maker** bundle. To create class `/src/Form/StudentType.php` we first enter CLI command:

```bash
    $ php bin/console make:form
```

You'll then be asked the form class name - by Symmfony convention we just add `Type` to the Entity class name:    
    
```bash
    The name of the form class (e.g. VictoriousPuppyType):
    > Student
```

You'll then be asked for the Entity name, so we enter `Student`:

```bash
    The name of Entity or fully qualified model class name that the new form will be bound to (empty for none):
    > Student
```

You'll then see output telling us that the make tool has generated Form class `StudentType` for us in the `src/Form/` directory:

```bash
    created: src/Form/StudentType.php

    Success! 

    Next: Add fields to your form and start using it.
    Find the documentation at https://symfony.com/doc/current/forms.html
```

If we look inside `/src/Form/StudentType.php` we see a skeleton class as follows:

```php
    namespace App\Form;
    
    use App\Entity\Student;
    use Symfony\Component\Form\AbstractType;
    use Symfony\Component\Form\FormBuilderInterface;
    use Symfony\Component\OptionsResolver\OptionsResolver;
    
    class StudentType extends AbstractType
    {
        public function buildForm(FormBuilderInterface $builder, array $options)
        {
            $builder
                ->add('firstName')
                ->add('surname')
            ;
        }
    
        public function configureOptions(OptionsResolver $resolver)
        {
            $resolver->setDefaults([
                'data_class' => Student::class,
            ]);
        }
    }
```

For the `/Form/StudentType.php` class we need to:

- add `use` statements for the `SubmitType` we want to use (it works out for itself the `TextType` from the Entity annotations)

   ```php
        use Symfony\Component\Form\Extension\Core\Type\SubmitType;
    ```


- write a statement to add a submit button to the form:

    ```php
            public function buildForm(FormBuilderInterface $builder, array $options)
            {
                $builder
                    ->add('firstName')
                    ->add('surname')
                    ->add('save', SubmitType::class, array('label' => 'Create Student'))
                ;

            }
    ```

That's our `StudentType` form class complete. 

For the `/Controller/StudentController.php` class we need to:

- remove the `use` statements for `TextType` and `SubmitType`

- add a `use` statement for the `StudentType` class we have just created:

   ```php
        use App\Form\StudentType;
    ```


- simplify our controller method, which can create the form in a single statement:

```php
     $form = $this->createForm(StudentType::class, $student);
```

So our refactored `new()` controller method looks as follows:

```php
    public function new(Request $request)
    {
        $student = new Student();

        $form = $this->createForm(StudentType::class, $student);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            return $this->createAction($student);
        }

        $template = 'student/new.html.twig';
        $argsArray = [
            'form' => $form->createView(),
        ];

        return $this->render($template, $argsArray);
    }
```

Figure \ref{html_validation} shows a screenshot of the HTML validation from the generated form (empty values not accepted due to =`required` attribute in the text `<input>` tags).

![HTML validation prevening empty text submissiomns. \label{html_validation}](./03_figures/part03/10_html_validation.png)




## Video tutorials about Symfony forms

Here are some resources on this topic:

- [Example of a numeric 'greater than' constraint in Entity class](https://symfony.com/doc/current/reference/constraints/GreaterThan.html)

- [Video: Code Review form validation with `@Assert`](https://codereviewvideos.com/course/beginner-s-guide-to-symfony-3-forms/video/validating-form-data-with-symfony-3)

