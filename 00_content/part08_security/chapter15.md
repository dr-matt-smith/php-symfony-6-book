
# Quickstart Symfony security

## Learn about Symfony security

There are several key Symfony reference pages to read when starting with security. These include:

- Introduction to security

    - [https://symfony.com/doc/current/security.html](https://symfony.com/doc/current/security.html)

- How to build a traditional login form

    - [https://symfony.com/doc/current/security/form_login_setup.html](https://symfony.com/doc/current/security/form_login_setup.html)

- Using CSRF protection

    - [https://symfony.com/doc/current/security/csrf.html](https://symfony.com/doc/current/security/csrf.html)

## New project with open and secured routes (project `security01`)

We are going to quickly create a 2-page website, with an open home page (url `/`) and a secured admin page (at url `/admin`).

## Create new project and add the security bundle library

Create a new project:

```bash
  symfony new --webapp security01
```

Add the fixtures ande Foundry pacakges (we'll need these later):

```bash
    composer require orm-fixtures
    composer require zenstruck/foundry
```


## Make a Default controller

Let's make a Default controller `/src/Controller/DefaultController.php`:

```bash
    symfony console make:controller Default
```

Edit the route URL to be simply `/`, and the internal name to be `homepage`. Also simplify the body of the method, so we can clearly see that we are passing an empty array of arguments to template `default/index.html.twig`:

```php
    #[Route('/', name: 'homepage')]
    public function index(): Response
    {
        $template = 'default/index.html.twig';
        $args = [];
        return $this->render($template, $args);
    }
```

Change the template `/templates/default/index.html.twig` to be something like:

```twig
    {% extends 'base.html.twig' %}

    {% block body %}
        welcome to the home page
    {% endblock %}
```

This will be accessible to everyone.

Test the server, and if necessary, remove the `encore_entry_` lines inside the `stylesheets` and `javascripts` blocks in the base template (`/templates/base.html.twig`).



## Make an un-secured Admin controller

Let's make a Admin controller:

```bash
    $ symfony console make:controller Admin
```


Change the default (index) admin template to be something like the following - a secret code we can only see if logged in.

File: `/templates/admin/index.html.twig`:

```twig
    {% extends 'base.html.twig' %}

    {% block body %}
        <h1>Admin home</h1>

        here is the secret code to the safe:
        007123
    {% endblock %}
```

That's it!

Run the web sever:

- visiting the Default page at `/` is fine, even though we have not logged in ag all

- visiting the Admin page at `/admin` is fine too (for now), since we haven't added any security to this controller/method yet

## Secure the Admin controller for only logged in users with `ROLE_ADMIN`

Let's now make the admin home page only accessible to only to users logged in with `ROLE_ADMIN` security.

Edit the new `AdminController` in `/src/Controller/AdminController.php`. Add a `use` statement, to let us use the `#[IsGranted]` attribute:

```php
    use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
```

Now we'll restrict access to the index action of our Admin controller using the `#[IsGranted]` attribute. Symfony security expects logged-in users to have one or more 'roles', these are simple text Strings in the form `ROLE_xxxx`. The default is to have all logged-in users having `ROLE_USER`, and they can have additional roles as well. So let's restrict our admin home page to only logged-in users that have the authentication `ROLE_ADMIN`:

```php
    ...
    use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

    class AdminController extends AbstractController
    {
        #[Route('/admin', name: 'app_admin')]
        #[IsGranted('ROLE_ADMIN')]
        public function index(): Response
        {
            $template = 'admin/index.html.twig';
            $args = [];
            return $this->render($template, $args);
        }
    }
```

NOTE: We can **make up** whatever roles are appropriate for our application, e.g.:

```php
    ROLE_ADMIN
    ROLE_STUDENT
    ROLE_PRESIDENT
    ROLE_TECHNICIAN
    ... etc. 
```

Now in the browser,  visiting the the `/admin` page should result in an HTTP 401 error (Unauthorized) due to insufficient authentication. See Figure \ref{not_authorised}.

![Screenshot of error attempting to visit `/admin`. \label{not_authorised}](./03_figures/part06_security/1_401_error.png){ width=75% }

Of course, we now need to add a way to login and define different user credentials etc...

## Core features about Symfony security

There are several related features and files that need to be understood when using the Symfony security system. These include:

- **firewalls**
- **providers** and **encoders**
- **route protection** (we met this with `#[IsGranted]` controller method attribute above...)
- user **roles** (we met this as part of `#[IsGranted]` above `("ROLE_ADMIN")` ...)

Core to Symfony security are the **firewalls** defined in `/config/packages/security.yml`. Symfony firewalls declare how route patterns are protected (or not) by the security system. Here is its default contents (less comments - lines starting with hash `#` character):

```yaml
    security:
        enable_authenticator_manager: true

        password_hashers:
            Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface: 'auto'

        providers:
            users_in_memory: { memory: null }
        firewalls:
            dev:
                pattern: ^/(_(profiler|wdt)|css|images|js)/
                security: false
            main:
                lazy: true
                provider: users_in_memory

        access_control:
            # - { path: ^/admin, roles: ROLE_ADMIN }
            # - { path: ^/profile, roles: ROLE_USER }
```

When no user has logged in, we can  this looking at the user information from the Symfony debug bar when visiting the default home page - see Figure \ref{no_user}.

![Symfony profiler showing no logged in authenticated user. \label{no_user}](./03_figures/part06_security/4_no_authentication.png)


A Symfony **provider** is where the security system can access a set of defined users of the web application. The default for a new project is simply `in_memory` - although non-trivial applications have users in a database or from a separate API. We see that the `main` firewall simply states that users are permitted (at present) any request route pattern, and anonymous authenticated users (i.e. ones who have not logged in) are permitted.

The `dev` firewall allows Symfony development tools (like the profiler) to work without any authentication required. Leave it in `security.yml` and just ignore the `dev` firewall from this point onwards.

## Generating the special `User` Entity class

Let's use the special `make:user` console command to create a `User` entity class that meets the requirements of providing user objects for the Symfony security system.

Enter the following at the command line, then just keep pressing `<RETURN>` to accept all the defaults:

```bash
    $ symfony console make:user

     The name of the security user class (e.g. User) [User]:
     >          // press <RETURN> to accept default
    
     Do you want to store user data in the database (via Doctrine)? (yes/no) [yes]:
     >          // press <RETURN> to accept default
    
     Enter a property name that will be the unique "display" name for the user (e.g. email, username, uuid) [email]:
     >          // press <RETURN> to accept default
    
     Will this app need to hash/check user passwords?
     Choose No if passwords are not needed or will be checked/hashed by some other system (e.g. a single sign-on server).

     Does this app need to hash/check user passwords? (yes/no) [yes]:
     >          // press <RETURN> to accept default
    
     created: src/Entity/User.php
     created: src/Repository/UserRepository.php
     updated: src/Entity/User.php
     updated: config/packages/security.yaml
      Success! 
```

## Review the changes to the `/config/packages/security.yml` file

If we look at `security.yml` it now begins as follows, taking into account our new `User` class:

```yaml
    security:
        enable_authenticator_manager: true

        password_hashers:
            Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface: 'auto'
            App\Entity\User:
                algorithm: auto

        providers:
            app_user_provider:
                entity:
                    class: App\Entity\User
                    property: email

```

We can see the the `auto` (best current security strength) algorithm will be used for hashing passwords to be stored in the DB. We also see that `App\Entity\User` is a provider of authenticated `User` objects, based on their `email` as a unique identifier.

## Migrate new `User` class to your database

Since we've changed our Entity classes, we should migrate these changes to the database (and, of course, first create your database if you have not already done so):

```bash
    symfony console make:migration
    symfony console doctrine:migrations:migrate
```

## Make some `User` fixtures

Let's make a user, by editing the existing `/src/DataFixtures/AppFixtures.php` class.

We'll use the Symfony sample code so that the plain-text passwords can be encoded (hashed) when stored in the database, see:

- [https://symfony.com/doc/current/security.html#registering-the-user-hashing-passwords](https://symfony.com/doc/current/security.html#registering-the-user-hashing-passwords)

Edit your class `UserFixtures` to make use of the `Passwordhasher`:

```php
    namespace App\DataFixtures;

    use Doctrine\Bundle\FixturesBundle\Fixture;
    use Doctrine\Persistence\ObjectManager;
    use App\Entity\User;
    use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


    class AppFixtures extends Fixture
    {
        private UserPasswordHasherInterface $passwordHasher;

        public function __construct(UserPasswordHasherInterface $passwordHasher)
        {
            $this->passwordHasher = $passwordHasher;
        }

        public function load(ObjectManager $manager)
        {
            // (1) create object
            $user = new User();
            $user->setEmail('matt.smith@smith.com');
            $user->setRoles(['ROLE_ADMIN', 'ROLE_TEACHER']);

            $plainPassword = 'smith';
            $encodedPassword = $this->passwordHasher->hashPassword($user, $plainPassword);

            $user->setPassword($encodedPassword);

            //(2) queue up object to be inserted into DB
            $manager->persist($user);

            // (3) insert objects into database
            $manager->flush();
        }
    }
```

From the template class generated for us, the first thing we need to do is add 2 `use` statements, to allow us to make use of the `User` entity class, and the `UserPasswordEncoderInterface` class:

```php
    use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
    use App\Entity\User;        
```

Next, to make it easy to encode passwords we'll add a new private instance variable `$passwordHasher`, and a constructor method to initialise this object:

```php
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }
```

Finally, we can write the code to create a new `User` object, set its `email` and `roles` properties, encode a plain text password and set the hashed value to the object. This `$user` object needs to then be added to the queue of objects for the database (`persist(...)`), and then finally inserted into the database (`flush()`):

```php
    public function load(ObjectManager $manager)
    {
        // (1) create object
        $user = new User();
        $user->setEmail('matt.smith@smith.com');
        $user->setRoles(['ROLE_ADMIN', 'ROLE_TEACHER']);

        $plainPassword = 'smith';
        $encodedPassword = $this->passwordHasher->hashPassword($user, $plainPassword);

        $user->setPassword($encodedPassword);

        //(2) queue up object to be inserted into DB
        $manager->persist($user);

        // (3) insert objects into database
        $manager->flush();
    }
```

NOTE: The `roles` property expects to be given an array of String roles, in the form `['ROLE_ADMIN', 'ROLE_SOMETHINGELSE', ...]`. These roles can be whatever we want for user:

```php
    $user->setRoles(['ROLE_ADMIN', 'ROLE_TEACHER']);
```
 
## Run and check your fixtures

Load the fixtures into the database (with `doctrine:fixtures:load`), and check them with a simple SQL query `select * from user`:

```bash
    symfony console doctrine:query:sql "select * from user"

 ---- ---------------------- -------------------------------- --------------------------------------------------------------
  id   email                  roles                            password
 ---- ---------------------- -------------------------------- --------------------------------------------------------------
  1    matt.smith@smith.com   ["ROLE_ADMIN", "ROLE_TEACHER"]   $2y$13$VUT7QvjVGP8xblXvc2mMnOdT0/JkOvpb5TrCiziHTms6jLsPoAt0e
 ---- ---------------------- -------------------------------- --------------------------------------------------------------
```

We can see the hashed password and roles `ROLE_ADMIN` and `ROLE_ADMIN`

## Creating a Login form

At present we have an authenticated user in the database, but no method for the user to authenticate themselves. Let's solve this by providing a login page for users...

One new additional to the maker tool in Symfony is automatic generation of a login form. Enter the following at the command line:

```bash
    symfony console make:auth
```

When prompted choose option `1`, a Login Form Authenticator:

```bash    
    What style of authentication do you want? [Empty authenticator]:
    [0] Empty authenticator
    [1] Login form authenticator
    > 1
```

Next, give the name `LoginFormAuthenticator` for this new authenticator:

```bash
    The class name of the authenticator to create (e.g. AppCustomAuthenticator):
    > LoginFormAuthenticator
```

Accept the default (press `<RETURN>`) for the name of your controller class (`SecurityController`):

```bash
     Choose a name for the controller class (e.g. SecurityController) [SecurityController]:
     > 
```

Accept the default (press `<RETURN>`) for creating a **logout** route (`yes`):

```bash
     Do you want to generate a '/logout' URL? (yes/no) [yes]:
     > 
```

You should now have a new controller `SecurityController`, a login form `templates/security/login.html.twig`, an authenticator class `LoginFormAuthenticator`, and an updated set of security settings `config/packages/security.yaml`:
```bash
     created: src/Security/LoginFormAuthenticator.php
     updated: config/packages/security.yaml
     created: src/Controller/SecurityController.php
     created: templates/security/login.html.twig

      Success! 
```

## Check the new routes

We can check we have new login/logout routes from with the `debug:router` command:

```bash
     symfony console debug:router
    Cannot load Xdebug - it was already loaded
     -------------------------- -------- -------- ------ ----------------------------------- 
      Name                       Method   Scheme   Host   Path                               
     -------------------------- -------- -------- ------ ----------------------------------- 
      _preview_error             ANY      ANY      ANY    /_error/{code}.{_format}           
        .... other _profiler debug routes here ...
      admin                      ANY      ANY      ANY    /admin                             
      homepage                   ANY      ANY      ANY    /                                  
      app_login                  ANY      ANY      ANY    /login                             
      app_logout                 ANY      ANY      ANY    /logout          
```

## Allow **any** user to view the login form 

Our full `security.yml` file should look as follows (with comments removed).algorithm
We can see added in the `main` firewall the custom authenticator `App\Security\LoginFormAuthenticator`, that we just generated.

```yaml
security:
    enable_authenticator_manager: true

    password_hashers:
        Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface: 'auto'
        App\Entity\User:
            algorithm: auto

    providers:
        app_user_provider:
            entity:
                class: App\Entity\User
                property: email

    firewalls:
        dev:
            pattern: ^/(_(profiler|wdt)|css|images|js)/
            security: false
        main:
            lazy: true
            provider: app_user_provider
            custom_authenticator: App\Security\LoginFormAuthenticator
            logout:
                path: app_logout

    access_control:
```

## Clear cache & visit `/admin`

Clear the cache (e.g. delete `/var/cache`), and open your browser to `/admin`. Since you are not currently logged-in, you should now be presented with a login form.

After we login with `matt.smith@smith.com` password = `smith`, we should now be re-directed to the admin page.


See in the Symfony Profiler footer that we are logged-in, and if we click this profiler footer, and then the `Security` link, we see this user has roles `ROLE_TEACHER` and `ROLE_ADMIN`, and **all** users automatically get `ROLE_USER`.

See Figure \ref{security01} this looking at the user information from the Symfony debug bar when visiting the default home page.

![Symfony profiler showing ROLE_TEACHER / USER / ADMIN authentication. \label{security01}](./03_figures/part06_security/new01_userToken.png)

## Using the `/logout` route

A logout route `/logout` was automatically added when we used the `make:auth` tool. So we can now use this route to logout the current user in several ways:

1. We can  enter the route directly in the browser address bar, e.g. via URL:

    ```
        http://localhost:8000/logout
    ```

1. We can also logout via the Symfony profile toolbar. See Figure \ref{logout_link}.

![Symfony profiler user logout action. \label{logout_link}](./03_figures/part06_security/6_logout_profiler.png){ width=75% }


In either case we'll logout any currently logged-in user, and return the anonymously authenticated user `anon` with no defined authentication roles.

## Finding and using the internal login/logout route names in `SecurityController`

Look inside the generated `/src/controller/SecurityController.php` file to see the  route attributes for our login/logout routes:

```php
    class SecurityController extends AbstractController
    {
        #[Route(path: '/login', name: 'app_login')]
        public function login(AuthenticationUtils $authenticationUtils): Response
        {
            // get the login error if there is one
            $error = $authenticationUtils->getLastAuthenticationError();

            // last username entered by the user
            $lastUsername = $authenticationUtils->getLastUsername();

            return $this->render('security/login.html.twig',
                ['last_username' => $lastUsername, 'error' => $error]
            );
        }

        #[Route(path: '/logout', name: 'app_logout')]
        public function logout(): void
        {
            throw new \LogicException(
                'This method can be blank - it will be intercepted by the logout key on your firewall.'
            );
        }
    }
```

We can add links for the user to login/logout on any page in a Twig template, by using the Twig `path(...)` function and passing it the internal route name for our logout route `app_logout`, e.g.

```twig
    <a href="{{ path('app_logout') }}">
        logout
    </a>
```
