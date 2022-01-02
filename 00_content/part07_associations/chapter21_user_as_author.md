
# Logged-in user stored as item author

## Getting User object for currently logged-in user

The Symfony security docs tell us how to get are reference to the currently logged-in user:

```php
    $user = $this->getUser();
```

or using the hinting and the param-converter:

```php
    use Symfony\Component\Security\Core\Security;
    
    ...
    
    public function someMethod(Security $security) 
    {
        $user = $security->getUser(); 
    }
```

Any non-trivial project involving databases involves one-to-many and many-to-many relationships. the Doctrine ORM system makes it very easy to declare, and manipulate datasets with foreign-key relationships.

Some useful information sources on this topic include:

- [How to Work with Doctrine Relations](http://symfony.com/doc/current/doctrine/associations.html)

- [Forms EntityType Field](http://symfony.com/doc/current/reference/forms/types/entity.html)


## Simple example: Users and their county (`associations05`)

First, create, or duplicate a basic user-authenticated secure Symfony website, e.g. project 9 with Twig:

- [https://github.com/dr-matt-smith/php-symfony4-book-codes-security-09-twig-security](https://github.com/dr-matt-smith/php-symfony4-book-codes-security-09-twig-security)

Next, create a `NewsItem` entity, with fields:

- title

- content

- author (which is a relationship associates to the `User` Entity)

Use the `make` interactive CLI tool for this. Create the Entity with text `title` and `content` properties as usual:

```bash 
    $ php bin/console make:entity NewsItem
    
     created: src/Entity/NewsItem.php
     created: src/Repository/NewsItemRepository.php
     
     Entity generated! Now let's add some fields!
     You can always add more fields later manually or by re-running this command.
    
     New property name (press <return> to stop adding fields):
     > title
    
     Field type (enter ? to see all types) [string]:
     > 
    
     Field length [255]:
     > 
    
     Can this field be null in the database (nullable) (yes/no) [no]:
     > 
    
     updated: src/Entity/NewsItem.php
    
     Add another property? Enter the property name (or press <return> to stop adding fields):
     > content

    ... etc.
```

Then add a property `author` linked as a `relation` Field Type to Entity `User` via a many-to-one relationship:

```bash
     Add another property? Enter the property name (or press <return> to stop adding fields):
     > author
    
     Field type (enter ? to see all types) [string]:
     > relation
    
     What class should this entity be related to?:
     > User
    
    What type of relationship is this?
     ------------ -------------------------------------------------------------------- 
      Type         Description                                                         
     ------------ -------------------------------------------------------------------- 
      ManyToOne    Each NewsItem relates to (has) one User.                            
                   Each User can relate to (can have) many NewsItem objects            
                                                                                       
      OneToMany    Each NewsItem can relate to (can have) many User objects.           
                   Each User relates to (has) one NewsItem                             
                                                                                       
      ManyToMany   Each NewsItem can relate to (can have) many User objects.           
                   Each User can also relate to (can also have) many NewsItem objects  
                                                                                       
      OneToOne     Each NewsItem relates to (has) exactly one User.                    
                   Each User also relates to (has) exactly one NewsItem.               
     ------------ -------------------------------------------------------------------- 
    
     Relation type? [ManyToOne, OneToMany, ManyToMany, OneToOne]:
     > ManyToOne
    
     Is the NewsItem.author property allowed to be null (nullable)? (yes/no) [yes]:
     > yes
    
     Do you want to add a new property to User so that you can access/update NewsItem objects from it - e.g. $user->getNewsItems()? (yes/no) [yes]:
     > yes
    
     A new property will also be added to the User class so that you can access the related NewsItem objects from it.
    
     New field name inside User [newsItems]:
     > 
    
     updated: src/Entity/NewsItem.php
     updated: src/Entity/User.php
```

Now:

- migrate your new Entity to the database

- generate CRUD for Entity `NewsItem`

## Add toString method to `User`

Since our CRUD will wish to list `User` records to associate with a new NewsItem, it will expect a `User` object to have a `__toString()` method, for how these users should be shown to the person creating the new `NewsItem`. So add the following `__toString()` method to Entity class `User`:

```php
    public function __toString()
    {
        return (string)$this->username;
    }
```

Test the system, run the webserver and visit the `/news/item` pages to test your CRUD.

## Use currently logged-in user as author

Let's automatically use the currently logged-in user as the author for a `NewsItem`:

```php
    $user = $this->getUser();
```

So we need to edit the CRUD code for the `new()` method in  `src/Controller/NewsItemConbtroller.php` to set the author to the currently logged-in user:

```php
    public function new(Request $request): Response
    {
        $user = $this->getUser();

        $newsItem = new NewsItem();
        $newsItem->setAuthor($user);    
```

SO the full listing for method `new()` is now:

```php
    /**
     * @Route("/new", name="news_item_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $user = $this->getUser();

        $newsItem = new NewsItem();
        $newsItem->setAuthor($user);

        $form = $this->createForm(NewsItemType::class, $newsItem);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($newsItem);
            $entityManager->flush();

            return $this->redirectToRoute('news_item_index');
        }

        return $this->render('news_item/new.html.twig', [
            'news_item' => $newsItem,
            'form' => $form->createView(),
        ]);
    }
```

When you visit the CRUD for for a new `NewsItem` you'll see the author is automatically populated with teh currently logged in user.

As an exercise, try to make this a read-only attribute, so that the author cannot be changed to a different user ...

## Protect CRUD so must be logged in

We'll get an error at present, if we create a `NewsItem` record when no logged in. So we need to secure the CRUD controller, requiring a user to be logged in to be allowed to create a new item.

By adding the `@IsGranted` annotation before the class declaration, this security requirement is enforced for **all** routes in this controller:

```php
    namespace App\Controller;
    
    use App\Entity\NewsItem;
    use App\Form\NewsItemType;
    use App\Repository\NewsItemRepository;
    use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Component\Routing\Annotation\Route;
    use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
    
    
    /**
     * @Route("/news/item")
     * @IsGranted("ROLE_ADMIN")
     */
    class NewsItemController extends AbstractController
    {
            ... as before
    }
```

