

# Symfony way of doing database CRUD

## Getting data into the DB

before we can test our DB entity class and repository, we need to get some data into the DB.

Let]'s create a new **route**, in the form: `/students/create/{firstName}/{surname}` that would create a new `Student` row in the DB table containing `{firstName}` and `{surname}`.

We also need to fix our controller, to be able to use the Doctrine DB repository class, rather than our previous D.I.Y. (do-it-yourself) repository class - but more of that later ...

## Creating new student records (project `db02`)

Let's add a new route and controller method to our `StudentController` class. This will define the `create()` method that receives parameter `$firstName`  and `$surname` extracted from the route `/student/create/{firstName}/{surname}`. This is all done automatically for us, through Symfony seeing the route parameters in the `Route(...)` attribute that immediately precedes the controller method. The 'signature' for our new `create(...)` method names 2 parameters that match those in the `#[Route(...)` annotation comment `create($firstName, $surame)`:

```php
   #[Route('/student/create/{firstName}/{surname}', name: 'student_create')]
   public function create(string $firstName, string $surname)
```


Creating a new `Student` object is straightforward, given `$firstName` and `$surname` from the URL-encoded GET name=value pairs:

```php
    $student = new Student();
    $student->setFirstName($firstName);
    $student->setSurname($surname);
```

Then we see the Doctrine code, to get a reference to an ORM object `EntityManager`, to tell it to store (`persist`) the object `$product`, and then we tell it to finalise (i.e. write to the database) any entities waiting to be persisted.


What we need is a special object reference `$doctrine` that is a `ManagerRegistry` object, and then we can write the following to create a variable `$em` that is a reference to the ORM  `EntityManager`:
```php
   $em = $doctrine->getManager();
```


One aspect of Symfony that make take some getting used to is how to get access to service objects like the `ManagerRegistry`. We add a typed argument to a controller method signature, and Symfony will **inject** a reference to the desired object. So to get a variable `$doctrine` that is a reference to the Doctrine `ManagerRegistry` we need to do 2 things:

1. Add an appropriate `use` statement at the top of the file

2. Add this as a parameter to the method signature: `ManagerRegistry $doctrine`

So the beginning of our class will have this new `use` statement:

```php
   <?php
   
   namespace App\Controller;
   
   use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
   use Symfony\Component\HttpFoundation\Response;
   use Symfony\Component\Routing\Annotation\Route;
   
   use App\Repository\StudentRepository;  
   use Doctrine\Persistence\ManagerRegistry;
```

And our method will look like this:

```php
    #[Route('/student/create/{firstName}/{surname}', name: 'student_create')]
    public function create(string $firstName, string $surname, ManagerRegistry $doctrine)
    {
        $student = new Student();
        $student->setFirstName($firstName);
        $student->setSurname($surname);
```


Let's now get that reference to the EntityManager:

```php
   $em = $doctrine->getManager();
```

Now we can use this `$em` EntityManager object to queue the new object for storate in the DB (persist), and then action all queded DB updated (flush):

```php
    $em->persist($student);
    $em->flush();
```

When the `$student` object has been successfully added to the DB, its `id` property will be updated to new auto-generated primary key integer. So we can access the ID of this object via `$student->getId()`.

Finally, let's create a `Response` message to the user telling them the ID of the newly created DB row. For this we need to add a `use` statement, so we can create a `Response` object.

So the code for our create controller method is:

```php
    <?php
   
    namespace App\Controller;
   
    use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Component\Routing\Annotation\Route;
   
    use App\Repository\StudentRepository;  
    use Doctrine\Persistence\ManagerRegistry;
   
    // we need to add a 'use' statement so we can create a Response object...
    use Symfony\Component\HttpFoundation\Response;
    ...
   
    #[Route('/student/create/{firstName}/{surname}', name: 'student_create')]
    public function create(string $firstName, string $surname, ManagerRegistry $doctrine): Response
    {
        $student = new Student();
        $student->setFirstName($firstName);
        $student->setSurame($surname);
   
        // entity manager
        $em = $doctrine->getManager();
   
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
    $ symfony console doctrine:query:sql "select * from student"

   .../vendor/doctrine/common/lib/Doctrine/Common/Util/Debug.php:71:
    array (size=1)
      0 =>
        array (size=3)
          'id' => string '1' (length=1)
          'first_name' => string 'matt' (length=4)
          'surname' => string 'smith' (length=5)

```

As usual, we can use the 2-letter shortcut to make writing this SQL query command a bit faster:


```bash
    $ symfony console do:qu:sql "select * from student"
```

## Updating the `list()` method to use Doctrine

Of course, we already have a route for viewing `Student` objects: `/student/list`. So we just have to update the code for this method to use the generated `StudentRepository` rather than our original D.I.Y. class.

If we have a reference to the ORM ManagerRegistry (`$doctrine`) we can get a reference to the repository class for any of our entity classes as follows:

```php
     $repositoryObject = $doctrine->getRepository(<EntityClass>::class);
```

so to get a `$studentRepository` object we write:

```php
     $studentRepository = $doctrine->getRepository(Student::class);
```

Again, we use the Symfony param-converter to **inject** an object reference for us, by simply adding a new parameter to the `list(...)` method signature. So our `list(...)` mehod now looks as follows:

```php
    #[Route('/student', name: 'student_list')]
    public function list(ManagerRegistry $doctrine): Response
    {
        $studentRepository = $doctrine->getRepository(Student::class);
        $students = $studentRepository->findAll();

        $template = 'student/list.html.twig';
        $args = [
            'students' => $students
        ];
        return $this->render($template, $args);
    }
 ```


## Doctrine Repository "free" methods

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


Figure \ref{student_list2} shows Twig HTML page listing all students generated from route `/student`.

![Listing all database student records with route `/student`. \label{student_list2}](./03_figures/part02/4_list_students_sm.png)

## Deleting by id

Let's define a delete route `/student/delete/{id}` and a `delete()` controller method. This method needs to first retreive the object (from the database) with the given ID, then ask to remove it, then flush the changes to the database (i.e. actually remove the record from the database). Note in this method we need both a reference to the entity manager `$em` and also to the student repository object `$studentRepository`:

```php
    #[Route('/student/delete/{id}', name: 'student_delete')]
    public function delete(int $id, ManagerRegistry $doctrine)
    {
        $studentRepository = $doctrine->getRepository(Student::class);
        $student = $studentRepository->find($id);

        // tells Doctrine you want to (eventually) delete the Student (no queries yet)
        $em = $doctrine->getManager();
        $em->remove($student);

        // actually executes the queries (i.e. the DELETE query)
        $em->flush();

        return new Response('Deleted student with id '.$id);
    }
```

## Updating given id and new name

We can do something similar to update. In this case we need 3 parameters: the id and the new first and surname. We'll also follow the Symfony examples (and best practice) by actually testing whether or not we were successful retrieving a record for the given id, and if not then throwing a 'not found' exception.

```php
    #[Route('/student/update/{id}/{newFirstName}/{newSurname}', name: 'student_update')]
    public function update(int $id, string $newFirstName, 
        string $newSurname, ManagerRegistry $doctrine)
    {
        $studentRepository = $doctrine->getRepository(Student::class);
        $student = $studentRepository->find($id);

//        $student = $doctrine->getRepository(Student::class)->find($id);

        $student->setFirstName($newFirstName);
        $student->setSurname($newSurname);

        $em = $doctrine->getManager();
        $em->flush();

        return $this->redirectToRoute('student_show', [
            'id' => $student->getId()
        ]);
    }
```

Until we write an error handler we'll get Symfony style exception pages, such as shown in Figure \ref{no_student_exception} when trying to update a non-existent student with id=99.

![Listing all database student records with route `/students/list`. \label{no_student_exception}](./03_figures/part02/5_404_no_student.png)

Note, to illustrate a few more aspects of Symfony some of the coding in `update()` has been written a little differently:

- we are getting the reference to the repository via the entity manager `$em->getRepository('App:Student')`
- we could also have 'chained' the `find($id)` method call onto the end of the code to get a reference to the repository (rather than storing the repository object reference and then invoking  `find($id)`). I.e. we could have written `$student = $doctrine->getRepository(Student::class)->find($id)`. This would be an example of using the 'fluent' interface^[read about it at [Wikipedia](https://en.wikipedia.org/wiki/Fluent_interface)] offered by Doctrine (where methods finish by returning an reference to their object, so that a sequence of method calls can be written in a single statement.
- rather than returning a `Response` containing a message, this controller method redirect the webapp to the route named `student_show` for the current object's `id`

We should also add the 'no student for id' test in our `delete()` method ...

## Updating our show action

We can now update our code in our `show(...)` to retrieve the record from the database:

```php
    #[Route('/student/{id}', name: 'student_show')]
    public function show(int $id, ManagerRegistry $doctrine): Response
    {
        $studentRepository = $doctrine->getRepository(Student::class);
        $student = $studentRepository->find($id);
```

So our full method for the show action looks as follows:

```php
    #[Route('/student/{id}', name: 'student_show')]
    public function show(int $id, ManagerRegistry $doctrine): Response
    {
        $studentRepository = $doctrine->getRepository(Student::class);
        $student = $studentRepository->find($id);

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

e.g. add an `update(...)` method to be as follows:

```php
    #[Route('/student/update/{id}/{newFirstName}/{newSurname}', name: 'student_update')]
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

So while we list an `{id}` parameter in the attribute preceding the method, in the method signautre itself we have a parmater that is a reference to a complete `Student` object, retrieved from the DB using the provided id value!

We can simplify our `show(...)` from:

```php
    #[Route('/student/{id}', name: 'student_show')]
    public function show(int $id, ManagerRegistry $doctrine): Response
    {
        $studentRepository = $doctrine->getRepository(Student::class);
        $student = $studentRepository->find($id);

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
    #[Route('/student/{id}', name: 'student_show')]
    public function show(Student $student): Response
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
    #[Route('/student/delete/{id}', name: 'student_delete')]
    public function delete(Student $student, ManagerRegistry $doctrine)
    {
        // store ID so can report it later
        $id = $student->getId();

        // tells Doctrine you want to (eventually) delete the Student (no queries yet)
        $em = $doctrine->getManager();
        $em->remove($student);

        // actually executes the queries (i.e. the DELETE query)
        $em->flush();

        return new Response('Deleted student with id '.$id);
    }
```

Likewise for update action:

```php
    #[Route('/student/update/{id}/{newFirstName}/{newSurname}', name: 'student_update')]
    public function update(Student $student, string $newFirstName, string $newSurname, ManagerRegistry $doctrine)
    {
        $student->setFirstName($newFirstName);
        $student->setSurname($newSurname);

        $em = $doctrine->getManager();
        $em->flush();

        return $this->redirectToRoute('student_show', [
            'id' => $student->getId()
        ]);
    }
```

NOTE - we will now get ParamConverter errors/exceptions rather than 404 errors if no record matches ID through ... so need to deal with those in a different way ...

## Creating the CRUD controller automatically from the CLI (project `db04`)

Here is something you might want to look into - automatic generation of controllers and Twig templates (we'll look at this in more detail in a later chapter).

NOTE: If trying out thew CRUD generation below, then make a copy of your current project, and try this out on the copy. Then discard the copy, so you can carry on working on your student project in the next chapter.

To try this out do the following:

1. Delete the `StudentController` class, since we'll be generating one automatically

2. Delete the `templates/student` directory, since we'll be generating those templates automatically

3. Delete the `var` directory, since we'll be generating one automatically

4. Then use the make crud command:
    
    ```bash
        $ symfony console make:crud Student
    ```
    
You should see the following output in the CLI:

```bash
    $ symfony console make:crud Student
    
     created: src/Controller/StudentController.php
     created: src/Form/Student1Type.php
     created: templates/student/_delete_form.html.twig
     created: templates/student/_form.html.twig
     created: templates/student/edit.html.twig
     created: templates/student/index.html.twig
     created: templates/student/new.html.twig
     created: templates/student/show.html.twig
                   
      Success! 
                   
     Next: Check your new CRUD by going to /student
``` 

You should find that you have now forms for creating and editing Student records, and controller routes for listing and showing records, and Twig templates to support all of this...

NOTE: As usually, if you get any messages about 'Route not found' or whatever, you need to delete the `/var/cache`,. Or the whole `/var` folder (as long as you aren't using an SQLite file in there instead of MySQL ...)

If you look at the code for controller methods like `show(...)` and `delete(...)` you'll find they are very similar to what we wrote by hand previously. For example the `show(...)` method should look something like the following:

```php
    #[Route('/{id}', name: 'student_show', methods: ['GET'])]
    public function show(Student $student): Response
    {
        return $this->render('student/show.html.twig', [
            'student' => $student,
        ]);
    }
```

which is just a more succinct way of writing the same as we had before

```php
    #[Route('/student/{id}', name: 'student_show')]
    public function show(Student $student): Response
    {
        $template = 'student/show.html.twig';
        $args = [
            'student' => $student
        ];

        return $this->render($template, $args);
    }
```

