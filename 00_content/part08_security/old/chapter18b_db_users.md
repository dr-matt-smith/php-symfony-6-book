
# Database-provided security users


## UPDATE STATUS
NOTE - these chapters are currently being updated for Symfony 4

The status of this chapter is:

- out of date (old Symfony 3)   <<<<<<<< current status
- updated and okay for now
- very up to date



## Overview of DB provided users (`recipe01`)

Having learnt about sessions and security piecemeal, let's now do things the normal way, with users provided to the authentication system from a database. In this chapter we'll start a new project from scratch, and implement the following:

- Bcrypt hashed passwords for users in an SQLite database table
- custom login form
- multiple user roles (ROLE_USER, ROLE_ADMIN)
- some simple use cases for a personal recipe case study

We'll use notes from the Symfony website:

- [Symfony security introduction](http://symfony.com/doc/current/security.html)
- [Loading users from a database](http://symfony.com/doc/current/security/entity_provider.html)


## New project with fixtures and SQLIte

Do the following

1. Create a new project named 'recipe01'.
    e.g.

    ```bash
        symfony new recipe01
    ```

1. Replace the default home page with something simpler

    e.g. replace the contents of `/app/Resources/views/default/index.html.twig` with the following:

    ```html
        {% extends 'base.html.twig' %}

        {% block body %}
           <h1>welcome to my-recipes</h1>

            <p>
                home page
            </p>
        {% endblock %}
    ```

1. Install and register the Doctrine fixture bundle

    follow the steps from Chapter \ref{chapter_fixtures}

    e.g.

    ```bash
        composer require --dev doctrine/doctrine-fixtures-bundle
    ```

    and

    ```php
        // file: /app/AppKernel.php
        class AppKernel extends Kernel
        {
            public function registerBundles()
            {
                $bundles = [
                    new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
                    ...
                ];

                if (in_array($this->getEnvironment(), ['dev', 'test'], true)) {
                    $bundles[] = new Symfony\Bundle\DebugBundle\DebugBundle();
                    ...
                    $bundles[] = new Doctrine\Bundle\FixturesBundle\DoctrineFixturesBundle();
                }
    ```

1. Setup SQLite

    follow the steps from Appendix \ref{appendix_db_sqlite}

    e.g.
    create a diretory `/var/data`, then update `config.yml` and `parameters.yml`

    ```bash
        FILE: config.yml
        doctrine:
            dbal:
                driver:   pdo_sqlite
                path:     "%kernel.root_dir%/%database_path%"

        FILE: parameters.yml
            database_path: ../var/data/data.sqlite
    ```

1. create your database

    ```bash
        php bin/console doctrine:database:create
    ```


## Create Recipe entity and its CRUD

Let's create a simple `Recipe` Entity, with CRUD controller and templates (unsecured at present).
Do the following:

1. create Entity Recipe

    ```bash
        php bin/console generate:doctrine:entity --no-interaction --entity=AppBundle:Recipe
        --fields="description:string(255) price:float"
    ```

    We will eventually add an `author` field for recipes, so we know which user they belong to...

1. update DB schema for entity

    ```bash
        php bin/console doctrine:schema:update --force
    ```

1. generate the CRUD for Entity `Recipe`

    ```bash
        php bin/console generate:doctrine:crud --entity=AppBundle:Recipe --format=annotation
        --with-write --no-interaction
    ```

## Creating our `User` entity

Create class `User.php` into AppBundle/Entity/User:

```php
    // src/AppBundle/Entity/User.php
    namespace AppBundle\Entity;

    use Doctrine\ORM\Mapping as ORM;
    use Symfony\Component\Security\Core\User\UserInterface;

    /**
     * @ORM\Table(name="app_users")
     * @ORM\Entity(repositoryClass="AppBundle\Repository\UserRepository")
     */
    class User implements UserInterface, \Serializable
    {
        /**
         * @ORM\Column(type="integer")
         * @ORM\Id
         * @ORM\GeneratedValue(strategy="AUTO")
         */
        private $id;

        /**
         * @ORM\Column(type="string", length=25, unique=true)
         */
        private $username;

        /**
         * @ORM\Column(type="string", length=64)
         */
        private $password;

        /**
         * @ORM\Column(type="string", length=60, unique=true)
         */
        private $email;

        /**
         * @ORM\Column(name="is_active", type="boolean")
         */
        private $isActive;

        public function __construct()
        {
            $this->isActive = true;
            // may not be needed, see section on salt below
            // $this->salt = md5(uniqid(null, true));
        }

        public function getUsername()
        {
            return $this->username;
        }

        public function getSalt()
        {
            // you *may* need a real salt depending on your encoder
            // see section on salt below
            return null;
        }

        public function getPassword()
        {
            return $this->password;
        }

        public function getRoles()
        {
            return array('ROLE_USER');
        }

        public function eraseCredentials()
        {
        }

        /** @see \Serializable::serialize() */
        public function serialize()
        {
            return serialize(array(
                $this->id,
                $this->username,
                $this->password,
                // see section on salt below
                // $this->salt,
            ));
        }

        /** @see \Serializable::unserialize() */
        public function unserialize($serialized)
        {
            list (
                $this->id,
                $this->username,
                $this->password,
                // see section on salt below
                // $this->salt
            ) = unserialize($serialized);
        }
    }
```

We need to generate the remaining accessor methods:

```bash
    php bin/console doctrine:generate:entities AppBundle/Entity/User
```

Now we can update the database schema based on the entity class:

```bash
    php bin/console doctrine:schema:update --force
```

We also need to create a `UserRepository` class to allow Doctrine to access this database stored entity. This should be created in `/src/AppBundle/Repository`. At this stage this can be an empty class, as long as it extends the Doctrine `Orm\EntityRepository` class:

```php
    namespace AppBundle\Repository;

    /**
     * UserRepository
     *
     * This class was generated by the Doctrine ORM. Add your own custom
     * repository methods below.
     */
    class UserRepository extends \Doctrine\ORM\EntityRepository
    {
    }

```

## Use Fixtures to create some users

We can now add some users to our database table using the Doctrine Fixtures bundle. Follow the steps in Chapter \ref{chapter_fixtures} to create 2 users:

- $userAdmin = $this->createActiveUser('admin', 'admin@admin.com', 'admin');
- $userMatt = $this->createActiveUser('matt', 'matt@matt.com', 'smith');

Both users (for this simple example) have username and password as the same text (username `matt` with password `matt` etc.).

## Setup security settings

We need to edit `/app/config/security.yml` to specify that we want to load users from our Doctrine entity. Also that we'll use simple (built-in) **HTTP** authentication for now. Edit `security.yml` to look as follows:

```yaml
    security:
        encoders:
            AppBundle\Entity\User:
                algorithm: bcrypt

        providers:
            our_db_provider:
                entity:
                    class: AppBundle:User
                    property: username

        firewalls:
            dev:
                pattern: ^/(_(profiler|wdt)|css|images|js)/
                security: false

            main:
                anonymous: ~
                provider: our_db_provider
                http_basic: ~
```

This will require login for any secured route, using the built-in login form of the client's browser.

Let's secure all routes relating to recipes. We can do this by adding a `@Route` annotation comment to the class comment (we could add one for each method's comment, but why make work for ourselves...).

So `RecipeController.php` should now have a `use` statement (so that Symfony annotations knows about the `Security` class), and a `@Route` security comment, stating that only logged-in users with `ROLE_USER` may access the recipe routes in this controller:

```php
    namespace AppBundle\Controller;

    use AppBundle\Entity\Recipe;
    use Symfony\Bundle\FrameworkBundle\Controller\Controller;
    use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
    use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
    use Symfony\Component\HttpFoundation\Request;
    use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

    /**
     * Recipe controller.
     *
     * @Route("recipe")
     * @Security("has_role('ROLE_USER')")
     */
    class RecipeController extends Controller
    {
        ... route methods go here ...
     }
```

Now attempting a recipe route, e.g. `http://localhost:8000/recipe` should result in a login form before access is permitted.

## Adding a custom login form and logout route

Obviously it would be nicer to have a customised login form, and useful to have a `logout` route, so let's do that.

We need to create the login form at `/app/Resources/views/security/login.html.twig`:

```html
    {% extends 'base.html.twig' %}

    {% block pagerTitle %}login{% endblock %}

    {% block body %}

        <h1>Login</h1>

        {% if error %}
            <div>{{ error.messageKey|trans(error.messageData, 'security') }}</div>
        {% endif %}

        <form action="{{ path('login') }}" method="post">

            <input type="hidden" name="_csrf_token"
                   value="{{ csrf_token('authenticate') }}"
            >

            <p>
            <label for="username">Username:</label>
            <input type="text" id="username" name="_username" value="{{ last_username }}" />

            <p>
            <label for="password">Password:</label>
            <input type="password" id="password" name="_password" />

            <p>
            <button type="submit">login</button>
        </form>

    {% endblock %}
```

We need to add a controller class to provide the route for our login form. In directory `/src/AppBundle/Controller` create class `SecurityController.php`:

```php
    <?php
    namespace AppBundle\Controller;

    use Symfony\Bundle\FrameworkBundle\Controller\Controller;
    use Symfony\Component\HttpFoundation\Request;
    use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

    class SecurityController extends Controller
    {
        /**
         * @Route("/login", name="login")
         */
        public function loginAction(Request $request)
        {
            $authenticationUtils = $this->get('security.authentication_utils');

            // get the login error if there is one
            $error = $authenticationUtils->getLastAuthenticationError();

            // last username entered by the user
            $lastUsername = $authenticationUtils->getLastUsername();

            // Twig stuff
            $templateName = 'security/login';
            $argsArray = [
                'last_username' => $lastUsername,
                'error'         => $error,
            ];

            return $this->render($templateName . '.html.twig', $argsArray);
        }
    }

```

We need to update `/app/config/security.yml` to specify that we'll use a form for login, and also define a logout route:

```yaml
    security:
        encoders:
            AppBundle\Entity\User:
                algorithm: bcrypt

        providers:
            our_db_provider:
                entity:
                    class: AppBundle:User
                    property: username

        firewalls:
            dev:
                pattern: ^/(_(profiler|wdt)|css|images|js)/
                security: false

            main:
                anonymous: ~
                provider: our_db_provider
                form_login:
                    login_path: login
                    check_path: login

                logout:
                    path:   /logout
                    target: /
```

We also need to declare that there is a `/logout` route (but we don't need to implmenet a controller action for this route. So we add the route in `/app/config/routing.yml`:

```yaml
    app:
        resource: "@AppBundle/Controller/"
        type:     annotation

    logout:
        path: /logout
```

