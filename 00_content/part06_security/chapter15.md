
# Quickstart Symfony security

NOTE:NOTE:NOTE:
NOTE:NOTE:NOTE:
NOTE:NOTE:NOTE:
NOTE:NOTE:NOTE:
NOTE:NOTE:NOTE:
NOTE:NOTE:NOTE:
NOTE:NOTE:NOTE:
NOTE:NOTE:NOTE:
NOTE:NOTE:NOTE:
NOTE:NOTE:NOTE:
NOTE:NOTE:NOTE:

New Symfony authenticator "passports"
see Symfonycasts videos:

https://symfonycasts.com/screencast/symfony-security/password-credentials

https://symfonycasts.com/screencast/symfony-security/csrf-token


## Learn about Symfony security

There are several key Symfony reference pages to read when starting with security. These include:

- [Introduction to security](https://symfony.com/doc/current/security.html)

- [How to build a traditional login form](https://symfony.com/doc/current/security/form_login_setup.html)

- [Using CSRF protection](https://symfony.com/doc/current/security/csrf.html)

## New project with open and secured routes (project `security01`)

We are going to quickly create a 2-page website, with an open home page (url `/`) and a secured admin page (at url `/admin`).

## Create new project and add the security bundle library

Create a new project:

```bash
    symfony new --full security01
```

Add the security bundle:

```bash
    composer req symfony/security-bundle
```

Add the fixtures bundle (we'll need this later):

```bash
    composer require orm-fixtures --dev 
```

## Make a Default controller

Let's make a Default controller `/src/Controller/DefaultController.php`:

```bash
    php bin/console make:controller Default
```

Edit the route to be `/` and the internal name to be `homepage`:

```php
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction()
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

## Make a secured Admin controller

Let's make a Admin controller:

```bash
    $ php bin/console make:controller Admin
```

This will be accessible to only to users logged in with `ROLE_ADMIN` security.

Edit the new `AdminController` in `/src/Controller/AdminController.php`. Add a `use` statement, to let us use the `@IsGranted` annotation:

```php
    use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
```

Now we'll restrict access to the index action of our Admin controller using the `@IsGranted` annotation. Symfony security expects logged-in users to have one or more 'roles', these are simple text Strings in the form `ROLE_xxxx`. The default is to have all logged-in users having `ROLE_USER`, and they can have additional roles as well. So let's restrict our admin home page to only logged-in users that have the authentication `ROLE_ADMIN`:

```php
    /**
     * @Route("/admin", name="admin")
     * @IsGranted("ROLE_ADMIN")
     */
    public function index()
    {
        $template = 'admin/index.html.twig';
        $args = [];
        return $this->render($template, $args);
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


Change the template `/templates/admin/index.html.twig` to be something like the following - a secret code we can only see if logged in:

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

- however, visiting the the `/admin` page should result in an HTTP 401 error (Unauthorized) due to insufficient authentication. See Figure \ref{not_authorised}.

![Screenshot of error attempting to visit `/admin`. \label{not_authorised}](./03_figures/part06_security/1_401_error.png){ width=75% }

Of course, we now need to add a way to login and define different user credentials etc...

## Core features about Symfony security

There are several related features and files that need to be understood when using the Symnfony security system. These include:

- **firewalls**
- **providers** and **encoders**
- **route protection** (we met this with `@IsGranded` controller method annotation comment above...)
- user **roles** (we met this as part of `@IsGranded` above `("ROLE_ADMIN")` ...)

Core to Symfony security are the **firewalls** defined in `/config/packages/security.yml`. Symfony firewalls declare how route patterns are protected (or not) by the security system. Here is its default contents (less comments - lines starting with hash `#` character):

```yaml
    security:
        providers:
            users_in_memory: { memory: null }

        firewalls:
            dev:
                pattern: ^/(_(profiler|wdt)|css|images|js)/
                security: false
            main:
                anonymous: lazy
                provider: users_in_memory
    
        access_control:
```

Symfony considers **every** request to have been authenticated, so if no login action has taken place then the request is considered to have been authenticated to be **anonymous** user `anon`. We can see in this `anon` user in Figure \ref{anon_user} this looking at the user information from the Symfony debug bar when visiting the default home page.

![Symfony profiler showing anonymous user authentication. \label{anon_user}](./03_figures/part06_security/4_anon_authentication.png){ width=50% }


A Symfony **provider** is where the security system can access a set of defined users of the web application. The default for a new project is simply `in_memory` - although non-trivial applications have users in a database or from a separate API. We see that the `main` firewall simply states that users are permitted (at present) any request route pattern, and anonymous authenticated users (i.e. ones who have not logged in) are permitted.

The `dev` firewall allows Symfony development tools (like the profiler) to work without any authentication required. Leave it in `security.yml` and just ignore the `dev` firewall from this point onwards.

## Generating the special `User` Entity class (project `security02`)

Let's use the special `make:user` console command to create a `User` entity class that meets the requirements of providing user objects for the Symfony security system.

Enter the following at the command line, then just keep pressing `<RETURN>` to accept all the defaults:

```bash
    $ php bin/console make:user

     The name of the security user class (e.g. User) [User]:
     >          // press <RETURN> to accept default
    
     Do you want to store user data in the database (via Doctrine)? (yes/no) [yes]:
     >          // press <RETURN> to accept default
    
     Enter a property name that will be the unique "display" name for the user (e.g. email, username, uuid) [email]:
     >          // press <RETURN> to accept default
    
     Will this app need to hash/check user passwords? Choose No if passwords are not needed or will be checked/hashed by some other system (e.g. a single sign-on server).
    
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
        encoders:
            App\Entity\User:
                algorithm: auto
    
        # https://symfony.com/doc/current/security.html#where-do-users-come-from-user-providers
        providers:
            # used to reload user from session & other features (e.g. switch_user)
            app_user_provider:
                entity:
                    class: App\Entity\User
                    property: email

```

## Migrate new `User` class to your database

Since we've changed our Entity classes, we should migrate these changes to the database (and, of course, first create your database if you havce not already done so):

```bash
    php bin/console make:migration
    php bin/console doctrine:migrations:migrate
```

## Make some `User` fixtures

Let's make some users with the `make:fixture` command:

```bash
    php bin/console make:fixture UserFixtures
```

We'll use the Symfony sample code so that the plain-text passwords can be encoded (hashed) when stored in the database, see:

- [https://symfony.com/doc/current/security.html#c-encoding-passwords](https://symfony.com/doc/current/security.html#c-encoding-passwords)

Edit your class `UserFixtures` to make use of the `PasswordEncoder`:

```php
    <?php    
    namespace App\DataFixtures;
    
    use Doctrine\Bundle\FixturesBundle\Fixture;
    use Doctrine\Common\Persistence\ObjectManager;
    
    use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
    use App\Entity\User;
    
    class UserFixtures extends Fixture
    {
         private $passwordEncoder;
    
         public function __construct(UserPasswordEncoderInterface $passwordEncoder)
         {
             $this->passwordEncoder = $passwordEncoder;
         }
    
        public function load(ObjectManager $manager)
        {
            // (1) create object
            $user = new User();
            $user->setEmail('matt.smith@smith.com');
            $user->setRoles(['ROLE_ADMIN', 'ROLE_TEACHER']);
    
            $plainPassword = 'smith';
            $encodedPassword = $this->passwordEncoder->encodePassword($user, $plainPassword);
    
            $user->setPassword($encodedPassword);
    
            //(2) queue up object to be inserted into DB
            $manager->persist($user);
    
            // (3) insert objects into database
            $manager->flush();
        }
    }
```

From the template class geneated for us, the first thing we need to do is add 2 `use` statements, to allow us to make use of the `User` entity class, and the `UserPasswordEncoderInterface` class:

```php
    use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
    use App\Entity\User;        
```

Next, to make it easy to encode passwords we'll add a new private instance variable `$passwordEncoder`, and a constructor method to initialise this object:

```php
     private $passwordEncoder;

     public function __construct(UserPasswordEncoderInterface $passwordEncoder)
     {
         $this->passwordEncoder = $passwordEncoder;
     }
```

Finally, we can write the code to create a new `User` object, set its `email` and `roles` properties, encode a plain text password and set the encoded value to the object. This `$user` object needs to then be added to the queue of objects for the database (`persist(...)`), and then finally inserted into the database (`flush()`):

```php
    public function load(ObjectManager $manager)
    {
        // (1) create object
        $user = new User();
        $user->setEmail('matt.smith@smith.com');
        $user->setRoles(['ROLE_ADMIN', 'ROLE_TEACHER']);

        $plainPassword = 'smith';
        $encodedPassword = $this->passwordEncoder->encodePassword($user, $plainPassword);

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
    php bin/console doctrine:query:sql "select * from user"
    Cannot load Xdebug - it was already loaded
    
    /php-symfony-5-book-codes-security-02-user/vendor/doctrine/dbal/lib/Doctrine/DBAL/Tools/Dumper.php:71:
    array (size=1)
      0 => 
        array (size=4)
          'id' => string '1' (length=1)
          'email' => string 'matt.smith@smith.com' (length=20)
          'roles' => string '["ROLE_USER", "ROLE_ADMIN"]' (length=27)
          'password' => string '$2y$13$BInaG05FUpAHqcEBtGG05.G.qDbT5SNHoCI1nBHb58FILxJxFUmPu' (length=60)
```

We can see the encoded password and roles `ROLE_USER` and `ROLE_ADMIN`

## Creating a Login form

One new additional to the maker tool in Symfony 5 is automatic generation of a login form. Enter the following at the commadn line:

```bash
    php bin/console make:auth
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
     php bin/console debug:router
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

Finally, we now have to edit our security firewall to allow **all** users, especially those not yet logged-in!, to access the `/login` route. Add the following line to the end of your `/config/packages/security.yml` configuration file:

```yaml
          - { path: ^/login$, roles: IS_AUTHENTICATED_ANONYMOUSLY }
```

So the full `security.yml` file should look as follows (with comments removed):

```yaml
    security:
        encoders:
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
                anonymous: lazy
                provider: app_user_provider
                guard:
                    authenticators:
                        - App\Security\LoginFormAuthenticator
                logout:
                    path: app_logout

        access_control:
          - { path: ^/login$, roles: IS_AUTHENTICATED_ANONYMOUSLY }
```

## Clear cache & visit `/admin`

Clear the cache (e.g. delete `/var/cache`), and open your browser to `/admin`. Since you are not currently logged-in, you should now be presented with a login form.

After we login with `matt.smith@smith.com` password = `smith`, we should now be able to see in the Symfony Profiler footer that we are logged in, and if we click this profiler footer, and then the `Security` link, we see this user has roles `ROLE_USER` and `ROLE_ADMIN`. 

See Figure \ref{anon_user} this looking at the user information from the Symfony debug bar when visiting the default home page.

![Symfony profiler showing ROLE_USER and ROLE_ADMIN authenticationm. \label{security01}](./03_figures/part06_security/new01_userToken.png)

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

Look inside the generated `/src/controller/SecurityController.php` file to see the annotation route comments for our login/lgout routes:

```php
    ...

    class SecurityController extends AbstractController
    {
        /**
         * @Route("/login", name="app_login")
         */
        public function login(AuthenticationUtils $authenticationUtils): Response
        {
            ...
        }
    
        /**
         * @Route("/logout", name="app_logout")
         */
        public function logout()
        {
            ...
        }
```

We can add links for the user to login/logout on any page in a Twig template, by using the Twig `url(...)` function and passing it the internal route name for our logout route `app_logout`, e.g.

```twig
    <a href="{{ url('app_logout') }}">
        logout
    </a>
```
