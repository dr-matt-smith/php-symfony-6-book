
# Doctrine associations (entity relationships)



## Some useful reference sources

Any non-trivial project involving databases involves one-to-many and many-to-many relationships. the Doctrine ORM system makes it very easy to declare, and manipulate datasets with foreign-key relationships.

Some useful information sources on this topuc include:

- [How to Work with Doctrine Relations](http://symfony.com/doc/current/doctrine/associations.html)

- [Forms EntityType Field](http://symfony.com/doc/current/reference/forms/types/entity.html)


## Simple example: Users and their county (`project22`)

Each User lives in a county (e.g. Matt Smith lives in County Kildare (in Ireland!)). So if we have a reference to a User object instance, then we want to easily be able to follow the foreign key link to the details of the county in which that User lives.

## Create the County Entity

**NOTE** First setup your project to either use MySQL or SQLite (see Appendices \ref{appendix_db_mysql} and \ref{appendix_db_sqlite}).

First let's generate a simple `County` Entity - it will have an automatically assigned integer `id`, and a text `name` property:

```bash
    php bin/console generate:doctrine:entity --no-interaction
    --entity=AppBundle:County --fields="name:string(255)"
```

You should now have a basic `County` Entity class in `/src/AppBundle/Entity/`.

## Create basic `User` entity

Now let's create a `User` entity, with `username`, `password` and a `county`. Since we are using an ORM we can specify that the `county` property of each user should be a reference to an object instance of class `AppBundle\Entity\County`:

```bash
    php bin/console generate:doctrine:entity --no-interaction
    --entity=AppBundle:User --fields="username:string(255) password:string(255) county:AppBundle\Entity\County"
```

## Update Entity `User` to declare many-to-one association

Change this entry for the `county` field in Entity `User` **from** this:

```php
    /**
     * @var \AppBundle\Entity\County
     *
     */
    private $county;
```

**to** the following (declaring the man to one relationship and creating a foreign key field 'county_id' to store the id for the relationship)

```php
	/**
     * @var \AppBundle\Entity\County
     *
     * @ORM\ManyToOne(targetEntity="County")
     * @ORM\JoinColumn(name="county_id", referencedColumnName="id")
     */
    private $county;
```

## Complete generation of Entities

We can now make Symfony generate getters and setters and complete the entity creation:

```bash
    php bin/console doctrine:generate:entities AppBundle
```

## Update the database schema

We now tell Symfony/Doctrine to update the database scheme to match our Entities:

```bash
     php bin/console doctrine:schema:update --force
```

## CRUD and views generation

We can now generate the CRUD controllers, `Type` form classes, and Twig views for CRUD actions for both our Entities User and County:

```bash

    php bin/console generate:doctrine:crud --entity=AppBundle:User --format=annotation
    --with-write --no-interaction

    php bin/console generate:doctrine:crud --entity=AppBundle:County --format=annotation
    --with-write --no-interaction
```

## MILESTONE 1 - we can now list users and work with counties

At this point, we can now list users, and work with counties (CRUD), as illustrated in Figure \ref{users_working}.

![Users list (index action) working. \label{users_working}](./03_figures/8_relationships/1_users_sm.png)

However, were we to try to create or edit a user, we'd get an error, since the default Form Type for `User` doesn't generate a drop-down meun based on the text `name` values for `County` entities.

## Editing the `UserType` form for county names

We need to make the `User` form generate a choice list from the different `County` entities in our database.

First we need toadd a 'use' statement for `/src/AppBundle/Form/UserType.php`:

```php
    use Symfony\Bridge\Doctrine\Form\Type\EntityType;
```

Then next we need to replace this one line:

```php
    $builder->add('username')->add('password')->add('county')        ;
```

with these 2 lines:

```php
    $builder->add('username')->add('password');

    $builder->add('county', EntityType::class, [
        'class' => 'App:County',

        // use the User.username property as the visible option string
        'choice_label' => 'name',
    ]);
```

Now our form should work, proving a drop-down choice menu when we edit or create a `User` record. We can see this in Figure \ref{choice_list}.


![New User form, with list of county names. \label{choice_list}](./03_figures/8_relationships/2_choice_list_sm.png)

## Add county names to Twig templates

Finally, we can add a `County` name details to our User **index** and **show** Twig templates.

1. Let's add County name in the list of users for the index USer action. We edit `app/Resources/views/user/index.html.twig` and add a `<th>` entry and for each User a `<td>` entry. We use dot `.` notation to show an object reference being followed, so we can literatally write `user.county.name` to the get the `name` property, of the `county` object that is referred to for the current `user` in the loop (see Figure \ref{users_list_with_county}):

```html
    <th>County</th>
    ...

    <td>{{ user.county.name }}</td>
```

![List of users, incuding their county. \label{users_list_with_county}](./03_figures/8_relationships/3_users_list_sm.png)


We do something similar for the **show** User action, adding another table row for the `County` (see Figure \ref{user_show_with_county}):

```html
    <tr>
        <th>County</th>
        <td>{{ user.county.name }}</td>
    </tr>
```

![Show one User, including their county. \label{user_show_with_county}](./03_figures/8_relationships/4_user_show_sm.png)

