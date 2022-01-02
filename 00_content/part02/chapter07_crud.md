

# Symfony approach to database CRUD


## Creating new student records (project `db02`)

Let's add a new route and controller method to our `StudentController` class. This will define the `create()` method that receives parameter `$firstName`  and `$surname` extracted from the route `/students/create/{firstName}/{surname}`. This is all done automatically for us, through Symfony seeing the route parameters in the `@Route(...)` annotation comment that immediately precedes the controller method. The 'signature' for our new `create(...)` method names 2 parameters that match those in the `@Route(...)` annotation comment `create($firstName, $surame)`:

```php
    /**
     * @Route("/student/create/{firstName}/{surname}")
     */
    public function create($firstName, $surname)
```


Creating a new `Student` object is straightforward, given `$firstName` and `$surname` from the URL-encoded GET name=value pairs:

```php
    $student = new Student();
    $student->setFirstName($firstName);
    $student->setSurname($surname);
```

Then we see the Doctrine code, to get a reference to the ORM `EntityManager`, to tell it to store (`persist`) the object `$product`, and then we tell it to finalise (i.e. write to the database) any entities waiting to be persisted:

```php
    $em = $this->getDoctrine()->getManager();
    $em->persist($student);
    $em->flush();
```

So the code for our create controller method is:

```php
    ...
    // we need to add a 'use' statement so we can create a Response object...
    use Symfony\Component\HttpFoundation\Response;
    ...

    /**
     * @Route("/student/create/{firstName}/{surname}")
     */
    public function create($firstName, $surname)
    {
        $student = new Student();
        $student->setFirstName($firstName);
        $student->setSurame($surname);

        // entity manager
        $em = $this->getDoctrine()->getManager();

        // tells Doctrine you want to (eventually) save the Product (no queries yet)
        $em->persist($student);

        // actually executes the queries (i.e. the INSERT query)
        $em->flush();

        return new Response('Created new student with id '.$student->getId());
    }
```

The above now means we can create new records in our database via this new route. So to create a record with name `matt smith` just visit this URL with your browser:

```
    http://localhost:8000/student/create/matt/smith
```

Figure \ref{new_student} shows how a new record `matt smith` is added to the database table via route `/student/create/{firstName}/{surname}`.

![Creating new student via route `/students/create/{firstName}/{surname}`. \label{new_student}](./03_figures/part02/2_new_student.png)

We can see these records in our database. Figure \ref{students_table} shows our new `students` table created for us.

![Controller created records in PHPMyAdmin. \label{students_table}](./03_figures/part02/3_workbench_new_student.png)


## Query database with SQL from CLI server

The `doctrine:query:sql` CLI command allows us to run SQL queries to our database directly from the CLI. Let's request all `Product` rows from table `product`:

```bash
    $ php bin/console doctrine:query:sql "select * from student"

   .../vendor/doctrine/common/lib/Doctrine/Common/Util/Debug.php:71:
    array (size=1)
      0 =>
        array (size=3)
          'id' => string '1' (length=1)
          'first_name' => string 'matt' (length=4)
          'surname' => string 'smith' (length=5)

```


## Updating the listAction() to use Doctrine

Doctrine creates repository objects for us. So we change the first line of method `list()` to the following:

```php
    $studentRepository = $this->getDoctrine()->getRepository('App:Student');
```

Doctrine repositories offer us lots of useful methods, including:

```php
    // query for a single record by its primary key (usually "id")
    $student = $repository->find($id);

    // dynamic method names to find a single record based on a column value
    $student = $repository->findOneById($id);
    $student = $repository->findOneByFirstName('matt');

    // find *all* products
    $students = $repository->findAll();

    // dynamic method names to find a group of products based on a column value
    $products = $repository->findBySurname('smith');
```

So we need to change the second line of of method `list()`  to use the `findAll()` repository method:

```php
    $students = $studentRepository->findAll();
```

Our listAction() method now looks as follows:

```php
    public function listAction()
    {
        $studentRepository = $this->getDoctrine()->getRepository('App:Student');
        $students = $studentRepository->findAll();

        $argsArray = [
            'students' => $students
        ];

        $templateName = 'students/list';
        return $this->render($templateName . '.html.twig', $argsArray);
    }
```

Figure \ref{student_list2} shows Twig HTML page listing all students generated from route `/student`.

![Listing all database student records with route `/student`. \label{student_list2}](./03_figures/part02/4_list_students_sm.png)

## Deleting by id

Let's define a delete route `/student/delete/{id}` and a `delete()` controller method. This method needs to first retreive the object (from the database) with the given ID, then ask to remove it, then flush the changes to the database (i.e. actually remove the record from the database). Note in this method we need both a reference to the entity manager `$em` and also to the student repository object `$studentRepository`:

```php
    /**
     * @Route("/student/delete/{id}")
     */
    public function delete($id)
    {
        // entity manager
        $em = $this->getDoctrine()->getManager();
        $studentRepository = $this->getDoctrine()->getRepository('App:Student');

        // find thge student with this ID
        $student = $studentRepository->find($id);

        // tells Doctrine you want to (eventually) delete the Student (no queries yet)
        $em->remove($student);

        // actually executes the queries (i.e. the DELETE query)
        $em->flush();

        return new Response('Deleted student with id '.$id);
    }
```

## Updating given id and new name

We can do something similar to update. In this case we need 3 parameters: the id and the new first and surname. We'll also follow the Symfony examples (and best practice) by actually testing whether or not we were successful retrieving a record for the given id, and if not then throwing a 'not found' exception.

```php
    /**
     * @Route("/student/update/{id}/{newFirstName}/{newSurname}")
     */
    public function update($id, $newFirstName, $newSurname)
    {
        $em = $this->getDoctrine()->getManager();
        $student = $em->getRepository('App:Student')->find($id);

        if (!$student) {
            throw $this->createNotFoundException(
                'No student found for id '.$id
            );
        }

        $student->setFirstName($newFirstName);
        $student->setSurname($newSurname);
        $em->flush();

        return $this->redirectToRoute('homepage');
    }
```

Until we write an error handler we'll get Symfony style exception pages, such as shown in Figure \ref{no_student_exception} when trying to update a non-existent student with id=99.

![Listing all database student records with route `/students/list`. \label{no_student_exception}](./03_figures/part02/5_404_no_student.png)

Note, to illustrate a few more aspects of Symfony some of the coding in `update()` has been written a little differently:

- we are getting the reference to the repository via the entity manager `$em->getRepository('App:Student')`
- we are 'chaining' the `find($id)` method call onto the end of the code to get a reference to the repository (rather than storing the repository object reference and then invoking  `find($id)`). This is an example of using the 'fluent' interface^[read about it at [Wikipedia](https://en.wikipedia.org/wiki/Fluent_interface)] offered by Doctrine (where methods finish by returning an reference to their object, so that a sequence of method calls can be written in a single statement.
- rather than returning a `Response` containing a message, this controller method redirect the webapp to the route named `homepage`

We should also add the 'no student for id' test in our `delete()` method ...

## Updating our show action

We can now update our code in our `show(...)` to retrieve the record from the database:

```php
    public function show($id)
    {
        $em = $this->getDoctrine()->getManager();
        $student = $em->getRepository('App:Student')->find($id);
```

So our full method for the show action looks as follows:

```php
    /**
     * @Route("/student/{id}", name="student_show")
     */
    public function shown($id)
    {
        $em = $this->getDoctrine()->getManager();
        $student = $em->getRepository('App:Student')->find($id);

        $template = 'student/show.html.twig';
        $args = [
            'student' => $student
        ];

        if (!$student) {
            $template = 'error/404.html.twig';
        }

        return $this->render($template, $args);
    }
```

We could, if we wish, throw a 404 error exception if no student records can be found for the given id, rather than rendering an error Twig template:

```php
    if (!$student) {
        throw $this->createNotFoundException(
            'No student found for id '.$id
        );
    }
```

## Redirecting to show after create/update

Keeping everything nice, we should avoid creating one-line and non-HTML responses like the following in `ProductController->create(...)`:

```php
    return new Response('Saved new product with id '.$product->getId());
```


Let's go back to the list page after a create or update action. Tell Symfony to redirect to the `student_show` route for

```php
    return $this->redirectToRoute('student_show', [
        'id' => $student->getId()
    ]);
```

e.g. refactor the `update()` method to be as follows:

```php
    /**
     * @Route("/student/update/{id}/{newFirstName}/{newSurname}")
     */
    public function update($id, $newFirstName, $newSurname)
    {
        $em = $this->getDoctrine()->getManager();
        $student = $em->getRepository('App:Student')->find($id);

        if (!$student) {
            throw $this->createNotFoundException(
                'No student found for id '.$id
            );
        }

        $student->setFirstName($newFirstName);
        $student->setSurname($newSurname);
        $em->flush();

        return $this->redirectToRoute('student_show', [
            'id' => $student->getId()
        ]);
    }
```


## Given `id` let Doctrine find Product automatically (project `db03`)

One of the features added when we installed the `annotations` bundle was the **Param Converter**.
Perhaps the most used param converter is when we can substitute an entity `id` for a reference to the entity itself.

We can simplify our `show(...)` from:

```php
    /**
     * @Route("/student/{id}", name="student_show")
     */
    public function show($id)
    {
        $em = $this->getDoctrine()->getManager();
        $student = $em->getRepository('App:Student')->find($id);

        $template = 'student/show.html.twig';
        $args = [
            'student' => $student
        ];

        if (!$student) {
            $template = 'error/404.html.twig';
        }

        return $this->render($template, $args);
    }
```

to just:

```php
    /**
     * @Route("/student/{id}", name="student_show")
     */
    public function show(Student $student)
    {
        $template = 'student/show.html.twig';
        $args = [
            'student' => $student
        ];

        if (!$student) {
            $template = 'error/404.html.twig';
        }

        return $this->render($template, $args);
    }
```

The Param-Converter will use the Doctrine ORM to go off, find the `ProductRepository`, run a `find(<id>)` query, and return the retrieved object for us!

Note - if there is no record in the database corresponding to the `id` then a 404-not-found error page will be generated.

Learn more about the Param-Converter on the Symfony documentation pages:

- [https://symfony.com/doc/current/doctrine.html#automatically-fetching-objects-paramconverter](https://symfony.com/doc/current/doctrine.html#automatically-fetching-objects-paramconverter)

- [http://symfony.com/doc/current/bundles/SensioFrameworkExtraBundle/annotations/converters.html](http://symfony.com/doc/current/bundles/SensioFrameworkExtraBundle/annotations/converters.html)


Likewise for delete action:

```php
        /**
         * @Route("/student/delete/{id}")
         */
        public function delete(Student $student)
        {
            // entity manager
            $em = $this->getDoctrine()->getManager();

            // store ID before deleting, so can report ID later
            $id = $student->getId();

            // tells Doctrine you want to (eventually) delete the Student (no queries yet)
            $em->remove($student);

            // actually executes the queries (i.e. the DELETE query)
            $em->flush();

            return new Response('Deleted student with id = '. $id);
        }
```

Likewise for update action:

```php
    /**
     * @Route("/student/update/{id}/{newFirstName}/{newSurname}")
     */
    public function update(Student $student, $newFirstName, $newSurname)
    {
        $em = $this->getDoctrine()->getManager();

        $student->setFirstName($newFirstName);
        $student->setSurname($newSurname);
        $em->flush();

        return $this->redirectToRoute('student_show', [
            'id' => $student->getId()
        ]);
    }
```

NOTE - we will now get ParamConverter errors rather than 404 errors if no record matches ID through ...

## Creating the CRUD controller automatically from the CLI (project `db04`)

Here is something you might want to look into - automatic generation of controllers and Twig templates (we'll look at this in more detail in a later chapter).

NOTE: If trying out thew CRUD generation below, then make a copy of your current project, and try this out on the copy. Then discard the copy, so you can carry on working on your student project in the next chapter.

To try this out do the following:

1. Delete the `StudentController` class, since we'll be generating one automatically

1. Delete the `templates/student` directory, since we'll be generating those templates automatically

1. Then use the make crud command:
    
    ```bash
        $ php bin/console make:crud Student
    ```
    
You should see the following output in the CLI:

```bash
    $ php bin/console make:crud Student
    
     created: src/Controller/StudentController.php
     created: src/Form/Student1Type.php
     created: templates/student/_delete_form.html.twig
     created: templates/student/_form.html.twig
     created: templates/student/edit.html.twig
     created: templates/student/index.html.twig
     created: templates/student/new.html.twig
     created: templates/student/show.html.twig
                   
      Success! 
                   
     Next: Check your new CRUD by going to /student/
``` 

You should find that you have now forms for creating and editing Student records, and controller routes for listing and showing records, and Twig templates to support all of this...

NOTE: As usualy, if you get any messages about 'Route not found' or whatever, you need to delete the `/var/cache` ...


