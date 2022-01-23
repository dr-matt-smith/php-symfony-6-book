# Adding a campus entity and relating it to Student 

## Create a new class: Campus

Figure  \ref{campus_class_diagram} shows a class diagram for a `Campus` entity class. 

![Class diagram for `Campus` class.\label{campus_class_diagram}](./03_figures/app_crud/campus_classDiagram1_toString.png){ width=100% }


Create entity `Campus` with single property 'location' (string)
HINT:

- use the interactive command line entity maker:

  - `symfony console make:entity Campus`
  - and add a (default type) string property named `location`

## Add `__toString()` method to `Campus` entity class

Now add a    `__toString()`  method to Campus class (`src/Entity/Campus.php`) containing the following
```php
    public function __toString(): string
    {
        return $this->location;
    }
```

We'll need this `__toString` method in `Campus` later, so that when creating/editing `Student`s we can choose the related `Campus` object from a **drop-down menu** - which needs a string description of each `Campus`.



## Generate CRUD for this Campus class

Generate CRUD for Campus:

```bash
    $ symfony console make:crud Campus
```

You'll see a new controller class, and some tempaltes in a folder `templates/campus`.

## Relate each Student to one Campus

Figure \ref{relationship_diagram} shows the two classes related. Each `Student` is related to exactly 1 `Campus` object, while each `Campus` object can be related to 0 or 1 or many `Student` objects.


![Class diagram for `Student`-`Campus` multiplicity.\label{relationship_diagram}](./03_figures/app_crud/student_campus.png){width=100%}



To create this relationship we are going to add a **'campus'** property to the `Student` class, that is a reference to a `Campus` object. Here is our detailed new `Student` class diagram:

![`Student` class diagram showing implemented relationship to `Campus` via new property `campus`.\label{student_related_diagram}](./03_figures/app_crud/student_relation.png){width=75%}



Here is how to add a related property to a class:

- add a property **`campus`** to the `Student` class, of type `relation` 
  - that is `ManyToOne` to the Campus class
  - i.e. many students linked to one campus

- to ADD a property to an existing class, we need to run the `make:entity` console command again:

```bash
symfony console make:entity Student
```
the console should see the entity already exists, and invite us to add a new property...

NOTE:

- once you've given the property name, type, class, and `ManyToOne` relationship type, just keep hitting `<RETURN>` to accept the defaults

    - Symfony will also add a `students` array property to the `Campus` class for you - that's fine
      
      - it's often handy, so if we have a reference to a `Campus` object then, for free, we get a property that is an array to all `Student` objects related to that `Campus` 

NOTE: very IMPORTANT - property type is **relation** not **string**

- do **NOT** create **string** property type for `campus`

- the type for property `campus` must be **relation**

    - this means the SQL generated in the migration will implement an SQL FOREIGN-KEY using the **id**s of Campus objects stored in a `campus_id` TABLE field for table `student`
    
    - look at the generated SQL when you make the **migration** after adding this relation property to campus

- if it's a string, then it won't link to a `Campus` object and things will not work later on ...

## Update Database Structure (since we changed our classes)

Create and run new DB migration

1. Create a migration by typing:

    ```bash
    symfony console make:migration
    ```
1. now run the migration by typing:

    ```bash
    symfony console doctrine:migrations:migrate
    ```

## Delete old CRUD and generate new CRUD for both classes

After having updated the structure of the `Student` entity class by adding the `campus` property, we now need to re-generate the HTML CRUD.

See Figure \ref{delete_crud} - we must remove the **old** CRUD files, and generate new CRUD, for things to now work with the relationship between `Student` and `Campus`.

NOTE: This is an **important step** - if you don't delete the old CRUD files, 
you won't be able to generate new CRUD making use of this relationship.

1. delete the old Student CRUD 
    - FILE:     `src/Controller/StudentController.php`
    - FILE:     `src/Form/StudentType.php`
    - folder:    `templates/student`
1. generate CRUD for Student

```bash
symfony console make:crud Student
```

![Deleting old Student CRUD files.\label{delete_crud}](./03_figures/app_crud/delete_student_crud.png){width=100%}



## Run server add some related records:

1. now run server:

    ```bash
        symfony serve
    ```
1.  visit the `Campus` CRUD pages and create record for 2 campuses 

    - e.g. **Blanch** and **Tallaght**

1. visit the `Student` CRUD pages, and edit / create Student's related to your new Campuses


![Screenshot showing list of Campus objects.](./03_figures/app_crud/crud14_campusScreenshot.png){ width=75% }

When we create/edit a student, we now get a dropdown menu of the Campus objects (the text in the dropdown menu is from the `__toString()` method we created for the Campus class).

![Screenshot showing new Student form with Campus choice dropdown menu](./03_figures/app_crud/crud15_dropDownScreenshot.png){ width=75% }



